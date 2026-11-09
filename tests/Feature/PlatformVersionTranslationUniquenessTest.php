<?php

declare(strict_types=1);

use App\Models\Lang;
use App\Models\PlatformVersion;
use App\Models\PlatformVersionTranslation;
use App\Models\User;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function platformVersionAdmin(): User
{
    $country = \App\Models\GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = \App\Models\User::create([
        'first_name' => 'Alice',
        'last_name' => 'Martin',
        'email' => 'admin-platform-uniqueness@site.com',
        'gender' => 'female',
        'phone' => '+22901111111',
        'password' => 'password',
        'status' => 'active',
        'country_id' => $country->id,
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function validVersionData(array $overrides = []): array
{
    $langFr = Lang::where('code', 'fr')->firstOrFail();
    $langEn = Lang::where('code', 'en')->firstOrFail();

    return array_merge([
        'code' => 'ver-' . strtolower(uniqid()),
        'status' => 'active',
        'is_published' => false,
        'is_default' => false,
        'released_at' => null,
        'translations' => [
            [
                'lang_id' => $langFr->id,
                'name' => 'Version Française ' . uniqid(),
                'description' => 'Description fr',
            ],
            [
                'lang_id' => $langEn->id,
                'name' => 'Version English ' . uniqid(),
                'description' => 'Description en',
            ],
        ],
    ], $overrides);
}

function buildStoreRules(array $translations): array
{
    $rules = [
        'code' => ['required', 'string', 'max:10', 'regex:/^[a-z0-9\\-]+$/', 'unique:platform_versions,code'],
        'status' => ['required', Rule::in(\App\Enums\PlatformVersions\PlatformVersionStatus::values())],
        'is_published' => ['boolean'],
        'released_at' => ['nullable', 'date', 'after_or_equal:today'],
        'translations' => ['required', 'array', 'min:1'],
        'translations.*.lang_id' => ['required', 'integer', 'exists:langs,id'],
        'translations.*.name' => ['required', 'string', 'max:255'],
        'translations.*.description' => ['required', 'string'],
    ];

    foreach ($translations as $index => $translation) {
        $langId = $translation['lang_id'] ?? null;

        $rules["translations.{$index}.name"] = [
            'required',
            'string',
            'max:255',
            Rule::unique('platform_version_translations', 'name')
                ->where(fn ($query) => $query->where('lang_id', $langId)),
        ];
    }

    return $rules;
}

function buildUpdateRules(array $translations): array
{
    $rules = [
        'code' => ['sometimes', 'required', 'string', 'max:10', 'regex:/^[a-z0-9\\-]+$/', 'unique:platform_versions,code'],
        'status' => ['sometimes', 'required', Rule::in(\App\Enums\PlatformVersions\PlatformVersionStatus::values())],
        'is_published' => ['boolean'],
        'released_at' => ['nullable', 'date'],
        'translations' => ['sometimes', 'required', 'array', 'min:1'],
        'translations.*.lang_id' => ['required', 'integer', 'exists:langs,id'],
        'translations.*.name' => ['required', 'string', 'max:255'],
        'translations.*.description' => ['required', 'string'],
    ];

    foreach ($translations as $index => $translation) {
        $langId = $translation['lang_id'] ?? null;
        $translationId = $translation['id'] ?? null;

        $uniqueRule = Rule::unique('platform_version_translations', 'name')
            ->where(fn ($query) => $query->where('lang_id', $langId));

        if ($translationId) {
            $uniqueRule->ignore($translationId, 'id');
        }

        $rules["translations.{$index}.name"] = [
            'required',
            'string',
            'max:255',
            $uniqueRule,
        ];
    }

    return $rules;
}

it('refuse un doublon de nom de traduction en base active (même lang_id)', function () {
    $langFr = Lang::where('code', 'fr')->firstOrFail();

    // Créer une première version avec une traduction "Version Alpha" en fr
    $v1 = PlatformVersion::create([
        'code' => 'ver-001',
        'status' => 'active',
        'is_published' => false,
    ]);
    PlatformVersionTranslation::create([
        'platform_version_id' => $v1->id,
        'lang_id' => $langFr->id,
        'name' => 'Version Alpha',
        'description' => 'Desc fr',
    ]);

    // Construire les règles comme dans StorePlatformVersionRequest
    $translations = [
        ['lang_id' => $langFr->id, 'name' => 'Version Alpha', 'description' => 'Desc fr v2'],
    ];

    $rules = buildStoreRules($translations);

    $data = [
        'code' => 'ver-002',
        'status' => 'active',
        'is_published' => false,
        'released_at' => null,
        'translations' => $translations,
    ];

    $validator = Validator::make($data, $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('translations.0.name'))->toBeTrue();
});

it('refuse un doublon de nom de traduction contre une ligne soft-deleted', function () {
    $langFr = Lang::where('code', 'fr')->firstOrFail();

    // Créer une version avec une traduction "Version Beta" en fr, puis la soft-deleter
    $v1 = PlatformVersion::create([
        'code' => 'ver-003',
        'status' => 'active',
        'is_published' => false,
    ]);
    $translation = PlatformVersionTranslation::create([
        'platform_version_id' => $v1->id,
        'lang_id' => $langFr->id,
        'name' => 'Version Beta',
        'description' => 'Desc fr',
    ]);
    $translation->delete(); // soft delete

    // Construire les règles comme dans StorePlatformVersionRequest
    $translations = [
        ['lang_id' => $langFr->id, 'name' => 'Version Beta', 'description' => 'Desc fr v2'],
    ];

    $rules = buildStoreRules($translations);

    $data = [
        'code' => 'ver-004',
        'status' => 'active',
        'is_published' => false,
        'released_at' => null,
        'translations' => $translations,
    ];

    $validator = Validator::make($data, $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('translations.0.name'))->toBeTrue();
});

it('accepte le même nom de traduction dans deux langues différentes', function () {
    $langFr = Lang::where('code', 'fr')->firstOrFail();
    $langEn = Lang::where('code', 'en')->firstOrFail();

    // Créer une première version avec "Version Gamma" en fr
    $v1 = PlatformVersion::create([
        'code' => 'ver-005',
        'status' => 'active',
        'is_published' => false,
    ]);
    PlatformVersionTranslation::create([
        'platform_version_id' => $v1->id,
        'lang_id' => $langFr->id,
        'name' => 'Version Gamma',
        'description' => 'Desc fr',
    ]);

    // Créer une deuxième version avec le même nom "Version Gamma" mais en en (langue différente)
    $translations = [
        ['lang_id' => $langEn->id, 'name' => 'Version Gamma', 'description' => 'Desc en'],
    ];

    $rules = buildStoreRules($translations);

    $data = [
        'code' => 'ver-006',
        'status' => 'active',
        'is_published' => false,
        'released_at' => null,
        'translations' => $translations,
    ];

    $validator = Validator::make($data, $rules);

    expect($validator->fails())->toBeFalse();
});


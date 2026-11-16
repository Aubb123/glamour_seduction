<?php

declare(strict_types=1);

use App\Models\Lang;
use App\Models\PlatformVersion;
use App\Models\PlatformVersionTranslation;
use Database\Seeders\LangSeeder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(LangSeeder::class);
});

function translatedFieldValidator(array $payload, ?int $parentId = null): array
{
    $request = new class extends FormRequest
    {
        public function check(array $payload, ?int $parentId): array
        {
            $this->replace($payload);
            $validator = Validator::make($payload, ['translations' => ['required', 'array']]);

            $translations = $this->input('translations', []);

            // Vérifie les doublons de lang_id dans le payload
            $langIds = collect($translations)->pluck('lang_id');

            if ($langIds->count() !== $langIds->unique()->count()) {
                $validator->errors()->add('translations', 'duplicate_lang_id');
            }

            // Vérifie l'unicité des champs traduits (name, description) par langue
            $seen = [];

            foreach ($translations as $key => $translation) {
                if (! is_array($translation)) {
                    continue;
                }

                $langId = $translation['lang_id'] ?? null;

                if (! $langId) {
                    continue;
                }

                foreach (['name', 'description'] as $field) {
                    $value = $translation[$field] ?? null;

                    if ($value === null || $value === '') {
                        continue;
                    }

                    // Doublon dans le payload courant
                    $fingerprint = serialize([(string) $langId, (string) $value]);

                    if (isset($seen[$fingerprint])) {
                        $validator->errors()->add(
                            "translations.{$key}.{$field}",
                            'duplicate_in_payload',
                        );

                        continue;
                    }

                    $seen[$fingerprint] = true;

                    // Doublon en base de données (exclut la traduction de la version courante si fournie)
                    $query = PlatformVersionTranslation::where('lang_id', $langId)
                        ->where($field, $value);

                    if ($parentId !== null) {
                        $query->where('platform_version_id', '!=', $parentId);
                    }

                    if ($query->exists()) {
                        $validator->errors()->add(
                            "translations.{$key}.{$field}",
                            'unique_in_db',
                        );
                    }
                }
            }

            return $validator->errors()->toArray();
        }
    };

    return $request->check($payload, $parentId);
}

function translatedFieldVersion(string $code): PlatformVersion
{
    return PlatformVersion::create([
        'code' => $code,
        'status' => 'inactive',
        'is_published' => false,
        'is_default' => false,
        'released_at' => null,
    ]);
}

function translatedFieldRow(PlatformVersion $version, int $langId, string $name, string $description): PlatformVersionTranslation
{
    return PlatformVersionTranslation::create([
        'platform_version_id' => $version->id,
        'lang_id' => $langId,
        'name' => $name,
        'description' => $description,
    ]);
}

it('rejects values already present on active translations', function (): void {
    $langId = Lang::where('code', 'fr')->value('id');
    $activeVersion = translatedFieldVersion('active-check');
    translatedFieldRow($activeVersion, $langId, 'Active name', 'Active description');

    $deletedVersion = translatedFieldVersion('deleted-check');
    translatedFieldRow($deletedVersion, $langId, 'Deleted name', 'Deleted description')->delete();

    $errors = translatedFieldValidator([
        'translations' => [
            ['lang_id' => $langId, 'name' => 'Active name', 'description' => 'New description'],
            ['lang_id' => $langId, 'name' => 'Deleted name', 'description' => 'Another description'],
            ['lang_id' => $langId, 'name' => 'Another name', 'description' => 'Deleted description'],
        ],
    ]);

    // Les traductions soft-deleted ne sont pas visibles via l'Eloquent standard (comme dans BlogPost)
    expect($errors)->toHaveKeys([
        'translations.0.name',
    ]);
});

it('allows the same translated values in different languages', function (): void {
    $fr = Lang::where('code', 'fr')->value('id');
    $en = Lang::where('code', 'en')->value('id');

    $errors = translatedFieldValidator([
        'translations' => [
            ['lang_id' => $fr, 'name' => 'Same name', 'description' => 'Same description'],
            ['lang_id' => $en, 'name' => 'Same name', 'description' => 'Same description'],
        ],
    ]);

    expect($errors)->toBeEmpty();
});

it('rejects duplicates submitted in the same payload for the same language', function (): void {
    $langId = Lang::where('code', 'fr')->value('id');

    $errors = translatedFieldValidator([
        'translations' => [
            ['lang_id' => $langId, 'name' => 'Repeated', 'description' => 'Repeated text'],
            ['lang_id' => $langId, 'name' => 'Repeated', 'description' => 'Repeated text'],
        ],
    ]);

    expect($errors)->toHaveKeys([
        'translations.1.name',
        'translations.1.description',
    ]);
});

it('preserves the current parent translation but still checks other parents', function (): void {
    $langId = Lang::where('code', 'fr')->value('id');
    $currentVersion = translatedFieldVersion('current-check');
    translatedFieldRow($currentVersion, $langId, 'Current name', 'Shared description');

    $otherVersion = translatedFieldVersion('other-check');
    translatedFieldRow($otherVersion, $langId, 'Other name', 'Shared description');

    $errors = translatedFieldValidator([
        'translations' => [
            ['lang_id' => $langId, 'name' => 'Current name', 'description' => 'Shared description'],
        ],
    ], $currentVersion->id);

    expect($errors)->toHaveKey('translations.0.description')
        ->and($errors)->not->toHaveKey('translations.0.name');
});

<?php

declare(strict_types=1);

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogPostComment;
use App\Models\BlogPostCommentReply;
use App\Models\BlogTag;
use App\Models\GeoCountry;
use App\Models\User;
use Database\Seeders\CloudFolderSeeder;
use Database\Seeders\CloudPlatformSeeder;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

/**
 * Étape 12 — Blog : Posts (`blog-posts`), Commentaires & Réponses.
 */

beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    $this->seed(CloudPlatformSeeder::class);
    $this->seed(CloudFolderSeeder::class);
    bootV1WebRoutes();
});

function postsAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin@site.com', 'gender' => 'female',
        'phone' => '+22900000000', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function createPostFixture(): array
{
    // Réutilise l'admin déjà créé par le test courant (email unique)
    $admin = User::where('email', 'admin@site.com')->first() ?? postsAdmin();

    $category = BlogCategory::create(['sort_order' => 3001, 'status' => 'active']);
    $tagA = BlogTag::create(['sort_order' => 3002, 'status' => 'active']);
    $tagB = BlogTag::create(['sort_order' => 3003, 'status' => 'active']);

    return [$admin, $category, [$tagA->id, $tagB->id]];
}

function postPayload(array $overrides = []): array
{
    [$admin, $category, $tagIds] = createPostFixture();

    $langFr = \App\Models\Lang::where('code', 'fr')->firstOrFail();
    $langEn = \App\Models\Lang::where('code', 'en')->firstOrFail();
    $platform = \App\Models\CloudPlatform::firstOrFail();
    $uid = uniqid();

    return array_merge([
        'category_id' => $category->id,
        'status' => 'draft',
        'sort_order' => random_int(1000, 99999),
        'tags' => $tagIds,
        'cloud_platform_uuid' => $platform->uuid,
        'image' => UploadedFile::fake()->create('post.jpg', 100, 'image/jpeg'),
        'translations' => [
            ['lang_id' => $langFr->id, 'title' => 'Article '.$uid, 'content' => '<p>Contenu FR complet.</p>'],
            ['lang_id' => $langEn->id, 'title' => 'Post '.$uid, 'content' => '<p>Full english content.</p>'],
        ],
    ], $overrides);
}

it('liste les posts avec DataTables', function () {
    $admin = postsAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.blog-posts.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('blog-posts-table');
    expect($html)->toContain("$('#blog-posts-table').DataTable");
});

it('affiche la page de création d’un article (avec Summernote)', function () {
    $admin = postsAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.blog-posts.create'))
        ->assertOk()
        ->getContent();

    // Summernote requis sur les formulaires de contenu
    expect($html)->toContain('summernote');
});

it('crée un article en brouillon avec image, tags et traductions', function () {
    $admin = postsAdmin();

    $this->actingAs($admin)
        ->post(route('backend.blog-posts.store'), postPayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    $post = BlogPost::firstOrFail();
    expect($post->translations()->count())->toBe(2);
    expect($post->tags()->count())->toBe(2);
    expect($post->cloudFiles()->count())->toBe(1);
    expect($post->time_read)->toBeGreaterThan(0);
});

it('calcule published_at et le statut programmé (scheduled)', function () {
    $admin = postsAdmin();

    $future = now()->addDays(2)->format('Y-m-d H:i:s');
    $this->actingAs($admin)
        ->post(route('backend.blog-posts.store'), postPayload([
            'status' => 'scheduled',
            'published_at' => $future,
        ]))
        ->assertRedirect()
        ->assertSessionHas('success');

    $post = BlogPost::where('status', 'scheduled')->firstOrFail();
    expect($post->published_at->format('Y-m-d'))->toBe(now()->addDays(2)->format('Y-m-d'));
});

it('refuse un article programmé sans date de publication', function () {
    $admin = postsAdmin();

    $this->actingAs($admin)
        ->from(route('backend.blog-posts.create'))
        ->post(route('backend.blog-posts.store'), postPayload([
            'status' => 'scheduled',
            'published_at' => null,
        ]))
        ->assertSessionHasErrors(['published_at']);

    expect(BlogPost::count())->toBe(0);
});

it('affiche le détail d’un article (show avec slug)', function () {
    $admin = postsAdmin();

    $this->actingAs($admin)->post(route('backend.blog-posts.store'), postPayload());
    $post = BlogPost::firstOrFail();
    $slug = $post->active_translation?->slug ?? 'null';

    $this->actingAs($admin)
        ->get(route('backend.blog-posts.show', ['blogPost' => $post->uuid, 'slug' => $slug]))
        ->assertOk();
});

it('affiche la page d’édition d’un article', function () {
    $admin = postsAdmin();

    $this->actingAs($admin)->post(route('backend.blog-posts.store'), postPayload());
    $post = BlogPost::firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.blog-posts.edit', ['blogPost' => $post->uuid]))
        ->assertOk();
});
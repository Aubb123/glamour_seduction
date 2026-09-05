<?php

declare(strict_types=1);

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogPostComment;
use App\Models\BlogPostCommentReply;
use App\Models\GeoCountry;
use App\Models\User;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Étape 12 — Blog : Commentaires (`blog-post-comments`) & Réponses.
 */

beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function commentsAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::where('email', 'admin@site.com')->first();
    if ($admin) {
        return $admin;
    }

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

function commentFixture(): array
{
    $admin = commentsAdmin();

    $category = \App\Models\BlogCategory::create(['sort_order' => 4001, 'status' => 'active']);
    $post = BlogPost::create([
        'category_id' => $category->id, 'user_id' => $admin->id,
        'status' => 'published', 'sort_order' => 1, 'published_at' => now()->subDay(),
    ]);

    $comment = BlogPostComment::create([
        'post_id' => $post->id, 'user_id' => $admin->id,
        'content' => 'Super article !', 'is_visible' => true,
    ]);

    $reply = BlogPostCommentReply::create([
        'comment_id' => $comment->id, 'user_id' => $admin->id,
        'content' => 'Merci pour votre retour !', 'is_visible' => true,
    ]);

    return [$admin, $post, $comment, $reply];
}

it('liste les commentaires avec DataTables', function () {
    commentFixture();

    $html = $this->actingAs(commentsAdmin())
        ->get(route('backend.blog-post-comments.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('blog-comments-table');
    expect($html)->toContain("$('#blog-comments-table').DataTable");
});

it('affiche le détail d’un commentaire', function () {
    [$admin, $post, $comment] = commentFixture();

    $this->actingAs($admin)
        ->get(route('backend.blog-post-comments.show', ['blogPostComment' => $comment->uuid]))
        ->assertOk()
        ->assertSee('Super article !');
});

it('affiche les réponses d’un commentaire (onglet)', function () {
    [$admin, $post, $comment, $reply] = commentFixture();

    $this->actingAs($admin)
        ->get(route('backend.blog-post-comments.replies', ['blogPostComment' => $comment->uuid]))
        ->assertOk();
});

it('désactive puis réactive un commentaire', function () {
    [$admin, $post, $comment] = commentFixture();

    $this->actingAs($admin)
        ->patch(route('backend.blog-post-comments.deactivate', ['blogPostComment' => $comment->uuid]))
        ->assertRedirect();

    expect($comment->fresh()->is_visible)->toBeFalse();

    $this->actingAs($admin)
        ->patch(route('backend.blog-post-comments.activate', ['blogPostComment' => $comment->uuid]))
        ->assertRedirect();

    expect($comment->fresh()->is_visible)->toBeTrue();
});

it('passe un commentaire en corbeille puis le restaure', function () {
    [$admin, $post, $comment] = commentFixture();

    // Un commentaire avec des réponses ne peut pas être supprimé
    // (dependentRelations = ['replies']) → on crée un commentaire SANS réponse.
    $loneComment = BlogPostComment::create([
        'post_id' => $post->id, 'user_id' => $admin->id,
        'content' => 'Commentaire sans réponse.', 'is_visible' => false,
    ]);

    $this->actingAs($admin)
        ->delete(route('backend.blog-post-comments.destroy', ['blogPostComment' => $loneComment->uuid]))
        ->assertRedirect();

    expect(BlogPostComment::find($loneComment->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.blog-post-comments.trashed'))
        ->patch(route('backend.blog-post-comments.restore', ['uuid' => $loneComment->uuid]))
        ->assertRedirect();

    expect(BlogPostComment::find($loneComment->id))->not->toBeNull();
});

it('liste les réponses avec DataTables', function () {
    commentFixture();

    $html = $this->actingAs(commentsAdmin())
        ->get(route('backend.blog-post-comment-replies.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('blog-comment-replies-table');
    expect($html)->toContain("$('#blog-comment-replies-table').DataTable");
});

it('affiche le détail d’une réponse', function () {
    [$admin, $post, $comment, $reply] = commentFixture();

    $this->actingAs($admin)
        ->get(route('backend.blog-post-comment-replies.show', ['blogPostCommentReply' => $reply->uuid]))
        ->assertOk()
        ->assertSee('Merci pour votre retour !');
});

it('désactive puis réactive une réponse', function () {
    [$admin, $post, $comment, $reply] = commentFixture();

    $this->actingAs($admin)
        ->patch(route('backend.blog-post-comment-replies.deactivate', ['blogPostCommentReply' => $reply->uuid]))
        ->assertRedirect();

    expect($reply->fresh()->is_visible)->toBeFalse();

    $this->actingAs($admin)
        ->patch(route('backend.blog-post-comment-replies.activate', ['blogPostCommentReply' => $reply->uuid]))
        ->assertRedirect();

    expect($reply->fresh()->is_visible)->toBeTrue();
});

it('protege l’accès aux commentaires/réponses (non connecté → login)', function () {
    $this->get('/backend/blog-post-comments/index')->assertRedirect(route('login'));
    $this->get('/backend/blog-post-comment-replies/index')->assertRedirect(route('login'));
});
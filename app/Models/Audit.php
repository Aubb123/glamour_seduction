<?php

namespace App\Models;

use App\Enums\Audits\AuditAction;
use App\Traits\HasAuditModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Audit extends Model
{
    use HasAuditModelBehaviors;

    // $fillable définit les champs qui peuvent être remplis en masse
    protected $fillable = [
        'user_id',
        'auditable_type',
        'auditable_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
    ];

    // $casts définit les types de données pour les champs
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Clé de route utilisée pour le binding implicite ({audit})
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Noms de paramètres de route résolus par binding implicite.
     * NB : 'uuid' (legacy) reste également accepté.
     */
    protected const BOUND_ROUTE_PARAMS = [
        'uuid',
        'blogPost',
        'user',
        'blogCategory',
        'blogTag',
        'blogPostComment',
        'blogPostCommentReply',
        'gallery',
        'faq',
        'subscribe',
        'cloudPlatform',
        'cloudFolder',
        'cloudFile',
        'contact',
        'audit',
        'activityLog',
        'role',
        'permission',
        'geoContinent',
        'geoCountry',
        'platformVersion',
        'subscribeHistory',
        'shop',
        'position',
        'productWarehouse',
    ];

    // Mapping des types auditable vers les routes show et leurs paramètres
    protected static array $routeMapping = [
        'App\Models\BlogPost' => ['route' => 'backend.blog-posts.show', 'params' => ['blogPost', 'slug']],
        'App\Models\BlogCategory' => ['route' => 'backend.blog-categories.show', 'params' => ['blogCategory', 'slug']],
        'App\Models\BlogTag' => ['route' => 'backend.blog-tags.show', 'params' => ['blogTag', 'slug']],
        'App\Models\User' => ['route' => 'backend.users.show', 'params' => ['user', 'slug']],
        'App\Models\Role' => ['route' => 'backend.roles.show', 'params' => ['role', 'slug']],
        'App\Models\Permission' => ['route' => 'backend.permissions.show', 'params' => ['permission', 'slug']],
        'App\Models\BlogPostComment' => ['route' => 'backend.blog-post-comments.show', 'params' => ['blogPostComment']],
        'App\Models\BlogPostCommentReply' => ['route' => 'backend.blog-post-comment-replies.show', 'params' => ['blogPostCommentReply']],
        'App\Models\Gallery' => ['route' => 'backend.galleries.show', 'params' => ['gallery', 'slug']],
        'App\Models\Faq' => ['route' => 'backend.faqs.show', 'params' => ['faq', 'slug']],
        'App\Models\Subscribe' => ['route' => 'backend.subscribes.show', 'params' => ['subscribe']],
        'App\Models\SubscribeHistory' => ['route' => 'backend.subscribe-histories.show', 'params' => ['subscribeHistory']],
        'App\Models\Contact' => ['route' => 'backend.contacts.show', 'params' => ['contact']],
        'App\Models\Audit' => ['route' => 'backend.audits.show', 'params' => ['audit']],
        'App\Models\ActivityLog' => ['route' => 'backend.activity-logs.show', 'params' => ['activityLog']],
        'App\Models\CloudPlatform' => ['route' => 'backend.cloud-platforms.show', 'params' => ['cloudPlatform', 'slug?']],
        'App\Models\CloudFolder' => ['route' => 'backend.cloud-folders.show', 'params' => ['cloudFolder']],
        'App\Models\CloudFile' => ['route' => 'backend.cloud-files.show', 'params' => ['cloudFile']],
        'App\Models\GeoContinent' => ['route' => 'backend.geocontinents.show', 'params' => ['geoContinent', 'slug']],
        'App\Models\GeoCountry' => ['route' => 'backend.geocountries.show', 'params' => ['geoCountry', 'slug']],
        'App\Models\PlatformVersion' => ['route' => 'backend.versions.show', 'params' => ['platformVersion', 'slug']],
        'App\Models\Shop' => ['route' => 'backend.shops.show', 'params' => ['shop', 'slug']],
        'App\Models\Position' => ['route' => 'backend.positions.show', 'params' => ['position', 'slug']],
        'App\Models\ProductWarehouse' => ['route' => 'backend.product-warehouses.show', 'params' => ['productWarehouse']],
    ];

    public function getEntityUrlAttribute(): ?string
    {
        if (in_array($this->action, ['deleted', 'force_deleted'])) {
            return null;
        }

        if (! $this->relationLoaded('auditable') || ! $this->auditable) {
            return null;
        }

        $mapping = static::$routeMapping[$this->auditable_type] ?? null;

        if (! $mapping) {
            return null;
        }

        $routeParams = [];

        foreach ($mapping['params'] as $param) {
            if (in_array($param, self::BOUND_ROUTE_PARAMS, true)) {
                $routeParams[$param] = $this->auditable->uuid;
            } elseif ($param === 'slug') {
                $routeParams['slug'] = $this->auditable->slug ?? $this->auditable->active_translation?->slug ?? 'null';
            }
        }

        if (! Route::has($mapping['route'])) {
            return null;
        }

        return route($mapping['route'], $routeParams);
    }

    public function getEntityTitleAttribute(): ?string
    {
        if (! $this->auditable) {
            return null;
        }

        return match ($this->auditable_type) {
            'App\Models\BlogPost' => $this->auditable->active_translation?->title,
            'App\Models\BlogCategory' => $this->auditable->active_translation?->name,
            'App\Models\BlogTag' => $this->auditable->active_translation?->name,
            'App\Models\User' => trim(($this->auditable->first_name ?? '').' '.($this->auditable->last_name ?? '')),
            'App\Models\Role' => $this->auditable->name ?? $this->auditable->active_translation?->name,
            'App\Models\Permission' => $this->auditable->name,
            'App\Models\BlogPostComment' => $this->auditable->content ?? $this->auditable->active_translation?->content,
            'App\Models\BlogPostCommentReply' => $this->auditable->content ?? $this->auditable->active_translation?->content,
            'App\Models\Gallery' => $this->auditable->active_translation?->title,
            'App\Models\Faq' => $this->auditable->active_translation?->question,
            'App\Models\Subscribe' => $this->auditable->email,
            'App\Models\SubscribeHistory' => null,
            'App\Models\Contact' => $this->auditable->fullname ?? $this->auditable->subject ?? $this->auditable->email,
            'App\Models\CloudPlatform' => $this->auditable->label,
            'App\Models\CloudFolder' => $this->auditable->folder_name,
            'App\Models\CloudFile' => $this->auditable->file_name,
            'App\Models\GeoContinent' => $this->auditable->active_translation?->name,
            'App\Models\GeoCountry' => $this->auditable->active_translation?->name,
            'App\Models\PlatformVersion' => $this->auditable->active_translation?->name,
            'App\Models\Shop' => $this->auditable->active_translation?->name,
            'App\Models\Position' => $this->auditable->active_translation?->name,
            'App\Models\ProductWarehouse' => $this->auditable->active_translation?->name ?? $this->auditable->code,
            default => null,
        } ?: null;
    }

    public function getEntityLabelAttribute(): string
    {
        $title = $this->entity_title;

        $type = match ($this->auditable_type) {
            'App\Models\BlogPost' => __('Article'),
            'App\Models\BlogCategory' => __('Catégorie'),
            'App\Models\BlogTag' => __('Tag'),
            'App\Models\User' => __('Utilisateur'),
            'App\Models\Role' => __('Rôle'),
            'App\Models\Permission' => __('Permission'),
            'App\Models\BlogPostComment' => __('Commentaire'),
            'App\Models\BlogPostCommentReply' => __('Réponse'),
            'App\Models\Gallery' => __('Galerie'),
            'App\Models\Faq' => __('FAQ'),
            'App\Models\Subscribe' => __('Abonné'),
            'App\Models\SubscribeHistory' => __('Historique'),
            'App\Models\Contact' => __('Message'),
            'App\Models\CloudPlatform' => __('Plateforme Cloud'),
            'App\Models\CloudFolder' => __('Dossier'),
            'App\Models\CloudFile' => __('Fichier cloud'),
            'App\Models\GeoContinent' => __('Continent'),
            'App\Models\GeoCountry' => __('Pays'),
            'App\Models\PlatformVersion' => __('Version'),
            'App\Models\Shop' => __('Boutique'),
            'App\Models\Position' => __('Poste'),
            'App\Models\ProductWarehouse' => __('Entrepôt'),
            default => __('Entité'),
        };

        $title = Str::limit($title, 45);

        return $title
            ? __(':type : :title', ['type' => $type, 'title' => $title])
            : __(':type', ['type' => $type]);
    }

    public function getActionBadgeAttribute(): string
    {
        return AuditAction::tryFrom($this->action)?->badgeClass() ?? 'badge-soft-secondary';
    }

    public function getActionIconAttribute(): string
    {
        return AuditAction::tryFrom($this->action)?->icon() ?? 'feather-circle';
    }

    public function getActionLabelAttribute(): string
    {
        return AuditAction::tryFrom($this->action)?->label() ?? (string) $this->action;
    }

    public function getOldValuesArrayAttribute(): ?array
    {
        $values = $this->old_values;

        if (is_string($values)) {
            return json_decode($values, true) ?: null;
        }

        return is_array($values) ? $values : null;
    }

    public function getNewValuesArrayAttribute(): ?array
    {
        $values = $this->new_values;

        if (is_string($values)) {
            return json_decode($values, true) ?: null;
        }

        return is_array($values) ? $values : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}

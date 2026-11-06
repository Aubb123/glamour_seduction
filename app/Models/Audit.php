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
     * Noms de paramètres de route résolus par binding implicite ({blogPost}, {user}, {gallery}, etc.).
     * Ces paramètres reçoivent directement l'UUID de l'entité lors de la génération des URLs.
     * NB : 'uuid' (legacy, modules non encore migrés vers le binding implicite) reste également accepté.
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
    ];

    /**
     * Retourne l'URL de la page de détail de l'entité auditée.
     * Retourne null si l'action est une suppression ou si l'entité n'existe plus.
     */
    public function getEntityUrlAttribute(): ?string
    {
        // Pas de lien pour les actions de suppression
        if (in_array($this->action, ['deleted', 'force_deleted'])) {
            return null;
        }

        // Vérifie que l'entité existe encore
        if (! $this->relationLoaded('auditable') || ! $this->auditable) {
            return null;
        }

        // Vérifie si le type est dans le mapping
        $mapping = static::$routeMapping[$this->auditable_type] ?? null;

        if (! $mapping) {
            return null;
        }

        // Construit les paramètres de la route
        $routeParams = [];

        foreach ($mapping['params'] as $param) {
            if (in_array($param, self::BOUND_ROUTE_PARAMS, true)) {
                $routeParams[$param] = $this->auditable->uuid;
            } elseif ($param === 'slug') {
                // Certains modèles ont un slug direct, d'autres via leur traduction active
                $routeParams['slug'] = $this->auditable->slug ?? $this->auditable->active_translation?->slug ?? 'null';
            }
        }

        // Vérifie que la route existe
        if (! Route::has($mapping['route'])) {
            return null;
        }

        // Retourne l'URL de la route
        return route($mapping['route'], $routeParams);
    }

    /**
     * Retourne le titre de l'entité auditée (ex: question d'une FAQ, titre d'un article...).
     */
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
            default => null,
        } ?: null;
    }

    /**
     * Retourne le libellé du type d'entité pour l'affichage du bouton.
     */
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
            default => __('Entité'),
        };

        $title = Str::limit($title, 45);

        return $title
            ? __(':type : :title', ['type' => $type, 'title' => $title])
            : __(':type', ['type' => $type]);
    }

    /**
     * Accesseur : classe CSS du badge selon l'action.
     */
    public function getActionBadgeAttribute(): string
    {
        return AuditAction::tryFrom($this->action)?->badgeClass() ?? 'badge-soft-secondary';
    }

    /**
     * Accesseur : icône Feather selon l'action.
     */
    public function getActionIconAttribute(): string
    {
        return AuditAction::tryFrom($this->action)?->icon() ?? 'feather-circle';
    }

    /**
     * Accesseur : libellé traduit de l'action.
     */
    public function getActionLabelAttribute(): string
    {
        return AuditAction::tryFrom($this->action)?->label() ?? (string) $this->action;
    }

    /**
     * Accesseur : old_values sous forme de tableau garanti.
     */
    public function getOldValuesArrayAttribute(): ?array
    {
        $values = $this->old_values;

        if (is_string($values)) {
            return json_decode($values, true) ?: null;
        }

        return is_array($values) ? $values : null;
    }

    /**
     * Accesseur : new_values sous forme de tableau garanti.
     */
    public function getNewValuesArrayAttribute(): ?array
    {
        $values = $this->new_values;

        if (is_string($values)) {
            return json_decode($values, true) ?: null;
        }

        return is_array($values) ? $values : null;
    }

    // Un audit appartient à un utilisateur
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Un audit peut appartenir à n'importe quel modèle auditable
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}

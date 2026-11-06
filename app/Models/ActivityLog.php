<?php

namespace App\Models;

use App\Traits\HasAuditModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasAuditModelBehaviors;

    // $fillable définit les champs qui peuvent être remplis en masse
    protected $fillable = [
        'user_id',
        'loggable_type',
        'loggable_id',
        'type',
        'action',
        'message',
        'context',
        'activity',
        'ip_address',
        'user_agent',
        'is_success',
    ];

    //  $casts définit les types de données pour les champs
    protected $casts = [
        'context' => 'array', // ✅ JSON automatiquement désérialisé
        'is_success' => 'boolean', // null = non défini (N/A), true = succès, false = échec
    ];

    // Clé de route utilisée pour le binding implicite ({activityLog})
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

    // Un journal d'activité appartient à un utilisateur
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Un journal peut appartenir à n'importe quel modèle loggable
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Accesseur : retourne la classe CSS du badge selon le type de log.
     */
    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'info' => 'badge-soft-info',
            'warning' => 'badge-soft-warning',
            'error' => 'badge-soft-danger',
            'debug' => 'badge-soft-secondary',
            default => 'badge-soft-primary',
        };
    }

    /**
     * Accesseur : retourne le contexte sous forme de tableau.
     * Le cast 'array' sur le champ context le désérialise déjà automatiquement,
     * mais cet accesseur garantit un tableau même si la valeur brute est une chaîne.
     */
    public function getContextDataAttribute(): ?array
    {
        $context = $this->context;

        if (is_string($context)) {
            return json_decode($context, true) ?: null;
        }

        return is_array($context) ? $context : null;
    }

    /**
     * Accesseur : libellé humain de l'activité, résolu dynamiquement pour la langue courante.
     *
     * Le champ `activity` stocke désormais une CLÉ de traduction i18n (non figée) et non plus
     * une chaîne déjà traduite. Les éventuels paramètres de remplacement (ex: :uuid) sont
     * stockés dans le contexte sous la clé `i18n_params`.
     *
     * Rétrocompatible : si `activity` contient une ancienne valeur déjà traduite (qui n'est pas
     * une clé existante), __() la retourne telle quelle.
     */
    public function getActivityLabelAttribute(): string
    {
        if (! $this->activity) {
            return '—';
        }

        // Récupère les paramètres de remplacement depuis le contexte
        $params = $this->context_data['i18n_params'] ?? [];

        return __($this->activity, is_array($params) ? $params : []);
    }

    // Mapping des clés de contexte vers les modèles, routes show et champs de titre
    protected static array $contextMapping = [
        'faq_uuid' => ['model' => Faq::class, 'route' => 'backend.faqs.show', 'params' => ['faq', 'slug'], 'title' => 'active_translation.question'],
        'category_uuid' => ['model' => BlogCategory::class, 'route' => 'backend.blog-categories.show', 'params' => ['blogCategory', 'slug'], 'title' => 'active_translation.name'],
        'tag_uuid' => ['model' => BlogTag::class, 'route' => 'backend.blog-tags.show', 'params' => ['blogTag', 'slug'], 'title' => 'active_translation.name'],
        'post_uuid' => ['model' => BlogPost::class, 'route' => 'backend.blog-posts.show', 'params' => ['blogPost', 'slug'], 'title' => 'active_translation.title'],
        'user_uuid' => ['model' => User::class, 'route' => 'backend.users.show', 'params' => ['user', 'slug'], 'title' => 'first_last'],
        'role_uuid' => ['model' => Role::class, 'route' => 'backend.roles.show', 'params' => ['role', 'slug'], 'title' => 'name'],
        'permission_uuid' => ['model' => Permission::class, 'route' => 'backend.permissions.show', 'params' => ['permission', 'slug'], 'title' => 'name'],
        'comment_uuid' => ['model' => BlogPostComment::class, 'route' => 'backend.blog-post-comments.show', 'params' => ['blogPostComment'], 'title' => 'content'],
        'reply_uuid' => ['model' => BlogPostCommentReply::class, 'route' => 'backend.blog-post-comment-replies.show', 'params' => ['blogPostCommentReply'], 'title' => 'content'],
        'gallery_uuid' => ['model' => Gallery::class, 'route' => 'backend.galleries.show', 'params' => ['gallery', 'slug'], 'title' => 'active_translation.title'],
        'subscribe_uuid' => ['model' => Subscribe::class, 'route' => 'backend.subscribes.show', 'params' => ['subscribe'], 'title' => 'email'],
        'history_uuid' => ['model' => SubscribeHistory::class, 'route' => 'backend.subscribe-histories.show', 'params' => ['subscribeHistory'], 'title' => null],
        'contact_uuid' => ['model' => Contact::class, 'route' => 'backend.contacts.show', 'params' => ['contact'], 'title' => 'fullname'],
        'audit_uuid' => ['model' => Audit::class, 'route' => 'backend.audits.show', 'params' => ['audit'], 'title' => null],
        'log_uuid' => ['model' => ActivityLog::class, 'route' => 'backend.activity-logs.show', 'params' => ['activityLog'], 'title' => null],
        'cloud_platform_uuid' => ['model' => CloudPlatform::class, 'route' => 'backend.cloud-platforms.show', 'params' => ['cloudPlatform', 'slug?'], 'title' => 'label'],
        'cloud_folder_uuid' => ['model' => CloudFolder::class, 'route' => 'backend.cloud-folders.show', 'params' => ['cloudFolder'], 'title' => 'folder_name'],
        'cloud_file_uuid' => ['model' => CloudFile::class, 'route' => 'backend.cloud-files.show', 'params' => ['cloudFile'], 'title' => 'file_name'],
        'continent_uuid' => ['model' => GeoContinent::class, 'route' => 'backend.geocontinents.show', 'params' => ['geoContinent', 'slug'], 'title' => 'active_translation.name'],
        'country_uuid' => ['model' => GeoCountry::class, 'route' => 'backend.geocountries.show', 'params' => ['geoCountry', 'slug'], 'title' => 'active_translation.name'],
        'version_uuid' => ['model' => PlatformVersion::class, 'route' => 'backend.versions.show', 'params' => ['platformVersion', 'slug'], 'title' => 'active_translation.name'],
        'shop_uuid' => ['model' => Shop::class, 'route' => 'backend.shops.show', 'params' => ['shop', 'slug'], 'title' => 'active_translation.name'],
    ];

    /**
     * Retourne l'entité liée au contexte du log (si identifiable).
     */
    public function getContextEntityAttribute(): ?Model
    {
        $context = $this->context_data;

        if (! $context) {
            return null;
        }

        foreach (static::$contextMapping as $key => $mapping) {
            if (isset($context[$key]) && $context[$key]) {
                $entity = $mapping['model']::where('uuid', $context[$key])->first();

                if ($entity) {
                    return $entity;
                }
            }
        }

        return null;
    }

    /**
     * Retourne l'URL de la page de détail de l'entité liée au log.
     * Priorise la relation polymorphe `loggable`, puis le mapping contextuel.
     */
    public function getEntityUrlAttribute(): ?string
    {
        // Priorité 1 : relation polymorphe `loggable`
        if ($this->relationLoaded('loggable') && $this->loggable) {
            $mapping = $this->findMappingByModel(get_class($this->loggable));

            if ($mapping && Route::has($mapping['route'])) {
                return route($mapping['route'], $this->buildRouteParams($this->loggable, $mapping['params']));
            }
        }

        // Priorité 2 : mapping contextuel existant (fallback)
        $context = $this->context_data;

        if (! $context) {
            return null;
        }

        foreach (static::$contextMapping as $key => $mapping) {
            if (isset($context[$key]) && $context[$key]) {
                $entity = $mapping['model']::where('uuid', $context[$key])->first();

                if (! $entity) {
                    return null;
                }

                // Construit les paramètres de la route
                $routeParams = [];

                foreach ($mapping['params'] as $param) {
                    if (in_array($param, self::BOUND_ROUTE_PARAMS, true)) {
                        $routeParams[$param] = $entity->uuid;
                    } elseif ($param === 'slug') {
                        $routeParams['slug'] = $entity->slug ?? $entity->active_translation?->slug ?? 'null';
                    } elseif ($param === 'slug?') {
                        $routeParams['slug'] = $entity->slug ?? $entity->active_translation?->slug ?? null;
                    }
                }

                if (! Route::has($mapping['route'])) {
                    return null;
                }

                return route($mapping['route'], $routeParams);
            }
        }

        return null;
    }

    /**
     * Retourne le titre de l'entité liée au contexte.
     */
    public function getEntityTitleAttribute(): ?string
    {
        // Priorité 1 : relation polymorphe `loggable`
        if ($this->relationLoaded('loggable') && $this->loggable) {
            return $this->getTitleFromEntity($this->loggable);
        }

        // Priorité 2 : mapping contextuel existant (fallback)
        $entity = $this->context_entity;

        if (! $entity) {
            return null;
        }

        $context = $this->context_data;

        foreach (static::$contextMapping as $key => $mapping) {
            if (isset($context[$key]) && $context[$key] && $entity->uuid === $context[$key]) {
                return $this->getTitleFromEntity($entity, $mapping['title']);
            }
        }

        return null;
    }

    /**
     * Retourne le libellé du type d'entité pour l'affichage du bouton.
     */
    public function getEntityLabelAttribute(): string
    {
        // Priorité 1 : relation polymorphe `loggable`
        if ($this->relationLoaded('loggable') && $this->loggable) {
            $type = $this->getTypeLabelFromModel(get_class($this->loggable));

            $title = Str::limit($this->getTitleFromEntity($this->loggable), 45);

            return $title
                ? __(':type : :title', ['type' => $type, 'title' => $title])
                : __(':type', ['type' => $type]);
        }

        // Priorité 2 : mapping contextuel existant (fallback)
        $context = $this->context_data;

        if (! $context) {
            return __('Entité');
        }

        $type = null;

        foreach (static::$contextMapping as $key => $mapping) {
            if (isset($context[$key]) && $context[$key]) {
                $type = match ($key) {
                    'faq_uuid' => __('FAQ'),
                    'category_uuid' => __('Catégorie'),
                    'tag_uuid' => __('Tag'),
                    'post_uuid' => __('Article'),
                    'user_uuid' => __('Utilisateur'),
                    'role_uuid' => __('Rôle'),
                    'permission_uuid' => __('Permission'),
                    'comment_uuid' => __('Commentaire'),
                    'reply_uuid' => __('Réponse'),
                    'gallery_uuid' => __('Galerie'),
                    'subscribe_uuid' => __('Abonné'),
                    'history_uuid' => __('Historique'),
                    'contact_uuid' => __('Message'),
                    'cloud_platform_uuid' => __('Plateforme Cloud'),
                    'cloud_folder_uuid' => __('Dossier'),
                    'continent_uuid' => __('Continent'),
                    'country_uuid' => __('Pays'),
                    'version_uuid' => __('Version'),
                    'shop_uuid' => __('Boutique'),
                    default => __('Entité'),
                };

                break;
            }
        }

        if (! $type) {
            return __('Entité');
        }

        $title = Str::limit($this->entity_title, 45);

        return $title
            ? __(':type : :title', ['type' => $type, 'title' => $title])
            : __(':type', ['type' => $type]);
    }

    /**
     * Trouve le mapping correspondant à un modèle.
     */
    protected function findMappingByModel(string $modelClass): ?array
    {
        foreach (static::$contextMapping as $mapping) {
            if ($mapping['model'] === $modelClass) {
                return $mapping;
            }
        }

        return null;
    }

    /**
     * Récupère le type d'entité pour l'affichage (ex: "Article", "Catégorie").
     */
    protected function getTypeLabelFromModel(string $modelClass): string
    {
        return match ($modelClass) {
            Faq::class => __('FAQ'),
            BlogCategory::class => __('Catégorie'),
            BlogTag::class => __('Tag'),
            BlogPost::class => __('Article'),
            User::class => __('Utilisateur'),
            Role::class => __('Rôle'),
            Permission::class => __('Permission'),
            BlogPostComment::class => __('Commentaire'),
            BlogPostCommentReply::class => __('Réponse'),
            Gallery::class => __('Galerie'),
            Subscribe::class => __('Abonné'),
            SubscribeHistory::class => __('Historique'),
            Contact::class => __('Message'),
            Audit::class => __('Audit'),
            ActivityLog::class => __('Journal d\'activité'),
            CloudPlatform::class => __('Plateforme Cloud'),
            CloudFolder::class => __('Dossier'),
            CloudFile::class => __('Fichier cloud'),
            GeoContinent::class => __('Continent'),
            GeoCountry::class => __('Pays'),
            PlatformVersion::class => __('Version'),
            Shop::class => __('Boutique'),
            default => __('Entité'),
        };
    }

    /**
     * Construit les paramètres de route à partir d'une entité.
     */
    protected function buildRouteParams(Model $entity, array $params): array
    {
        $routeParams = [];

        foreach ($params as $param) {
            if (in_array($param, self::BOUND_ROUTE_PARAMS, true)) {
                $routeParams[$param] = $entity->uuid;
            } elseif ($param === 'slug') {
                $routeParams['slug'] = $entity->slug ?? $entity->active_translation?->slug ?? 'null';
            } elseif ($param === 'slug?') {
                $routeParams['slug'] = $entity->slug ?? $entity->active_translation?->slug ?? null;
            }
        }

        return $routeParams;
    }

    /**
     * Récupère le titre d'une entité selon le mapping contextuel.
     */
    protected function getTitleFromEntity(Model $entity, ?string $titleField = null): ?string
    {
        if (! $titleField) {
            // Découvre automatiquement le titre depuis le mapping
            $mapping = $this->findMappingByModel(get_class($entity));
            $titleField = $mapping['title'] ?? null;
        }

        if (! $titleField) {
            return null;
        }

        // Supporte les chemins imbriqués comme 'active_translation.question'
        $value = $entity;

        foreach (explode('.', $titleField) as $segment) {
            $value = $value?->{$segment};
        }

        return $value ? (string) $value : null;
    }
}

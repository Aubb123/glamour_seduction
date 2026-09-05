<?php

namespace App\Services\V1;

use App\Models\ActivityLog;
use App\Models\Audit;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\BlogPost;
use App\Models\BlogPostComment;
use App\Models\BlogPostCommentReply;
use App\Models\BlogPostTranslation;
use App\Models\BlogTag;
use App\Models\BlogTagTranslation;
use App\Models\CloudFile;
use App\Models\CloudFolder;
use App\Models\CloudPlatform;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\FaqTranslation;
use App\Models\Gallery;
use App\Models\GalleryTranslation;
use App\Models\GeoContinent;
use App\Models\GeoContinentTranslation;
use App\Models\GeoCountry;
use App\Models\GeoCountryTranslation;
use App\Models\Lang;
use App\Models\Permission;
use App\Models\PlatformVersion;
use App\Models\PlatformVersionTranslation;
use App\Models\Role;
use App\Models\RoleTranslation;
use App\Models\Subscribe;
use App\Models\SubscribeHistory;
use App\Models\User;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ActivityLogger
{
    use LogActivity;

    /**
     * Enregistre une entrée d'activité (log fichier + table activity_logs).
     */
    public function log(
        string $type, // info | warning | error | debug
        string $action,
        array $context = [],
        ?string $message = null,
        ?string $activity = null,
        ?bool $isSuccess = null,
        ?Model $loggable = null,
    ): void {
        $this->logActivity(
            type: $type,
            action: $action,
            context: $context,
            message: $message,
            activity: $activity,
            is_success: $isSuccess,
            loggable: $loggable,
        );
    }

    /**
     * Journalise un échec de résolution de modèle pour le binding implicite.
     * Appelé depuis resolveRouteBinding() des modèles migrés, qui retournera null
     * → Laravel lèvera alors la ModelNotFoundException → le 404 standard.
     */
    public function reportNotFoundForModel(string $modelClass, mixed $value, ?string $field = null): void
    {
        $map = self::modelMap();

        if (! isset($map[$modelClass])) {
            return;
        }

        [$actionPrefix, $langKey] = $map[$modelClass];

        $routeName = request()->route()?->getName();
        $action = $routeName !== null
            ? $routeName.'.binding_not_found'
            : $actionPrefix.'.binding_not_found';

        $this->log(
            type: 'warning',
            action: $action,
            context: [
                'resource_key' => $field ?? 'uuid',
                'resource_id' => (string) $value,
                'resource_class' => $modelClass,
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'i18n_params' => ['uuid' => (string) $value],
            ],
            message: null,
            activity: $langKey,
            isSuccess: false,
        );
    }

    public static function modelMap(): array
    {
        return [
            PlatformVersion::class => [
                'backend.versions',
                'v1/backend/views/pages/platform-versions/messages/platform-messages-controller.versions.not_found',
            ],
            PlatformVersionTranslation::class => [
                'backend.platform-version-translations',
                'v1/backend/views/pages/platform-versions/messages/platform-messages-controller.versions.translations.not_found',
            ],
            BlogCategory::class => [
                'backend.blog-categories',
                'v1/backend/views/pages/blogs/categories/messages/categories-messages-controller.categories.not_found',
            ],
            BlogCategoryTranslation::class => [
                'backend.blog-category-translations',
                'v1/backend/views/pages/blogs/categories/messages/categories-messages-controller.categories.translations.not_found',
            ],
            BlogPost::class => [
                'backend.blog-posts',
                'v1/backend/views/pages/blogs/posts/messages/posts-messages-controller.posts.not_found',
            ],
            BlogPostComment::class => [
                'backend.blog-post-comments',
                'v1/backend/views/pages/blogs/comments/messages/comments-messages-controller.comments.not_found',
            ],
            BlogPostCommentReply::class => [
                'backend.blog-post-comment-replies',
                'v1/backend/views/pages/blogs/comment-replies/messages/replies-messages-controller.replies.not_found',
            ],
            Faq::class => [
                'backend.faqs',
                'v1/backend/views/pages/faqs/messages/faqs-messages-controller.faqs.not_found',
            ],
            FaqTranslation::class => [
                'backend.faq-translations',
                'v1/backend/views/pages/faqs/messages/faqs-messages-controller.faqs.translations.not_found',
            ],
            Subscribe::class => [
                'backend.subscribes',
                'v1/backend/views/pages/subscribes/messages/subscribes-messages-controller.subscribes.not_found',
            ],
            SubscribeHistory::class => [
                'backend.subscribe-histories',
                'v1/backend/views/pages/subscribes/messages/subscribes-messages-controller.subscribes.histories.not_found',
            ],
            BlogPostTranslation::class => [
                'backend.blog-post-translations',
                'v1/backend/views/pages/blogs/posts/messages/posts-messages-controller.posts.translations.not_found',
            ],
            BlogTag::class => [
                'backend.blog-tags',
                'v1/backend/views/pages/blogs/tags/messages/tags-messages-controller.tags.not_found',
            ],
            BlogTagTranslation::class => [
                'backend.blog-tag-translations',
                'v1/backend/views/pages/blogs/tags/messages/tags-messages-controller.tags.translations.not_found',
            ],
            Lang::class => [
                'backend.langs',
                'v1/backend/views/pages/langs/messages/lang-messages-controller.langs.not_found',
            ],
            GeoContinent::class => [
                'backend.geocontinents',
                'v1/backend/views/pages/geos/continents/messages/continent-messages-controller.geocontinents.not_found',
            ],
            GeoContinentTranslation::class => [
                'backend.geocontinents.translation',
                'v1/backend/views/pages/geos/continents/messages/continent-messages-controller.geocontinents.translations.not_found',
            ],
            GeoCountry::class => [
                'backend.geocountries',
                'v1/backend/views/pages/geos/countries/messages/countries-messages-controller.countries.not_found',
            ],
            GeoCountryTranslation::class => [
                'backend.geocountries.translation',
                'v1/backend/views/pages/geos/countries/messages/countries-messages-controller.countries.translations.not_found',
            ],
            Gallery::class => [
                'backend.galleries',
                'v1/backend/views/pages/galleries/messages/galleries-messages-controller.galleries.not_found',
            ],
            GalleryTranslation::class => [
                'backend.gallery-translations',
                'v1/backend/views/pages/galleries/messages/galleries-messages-controller.galleries.translations.not_found',
            ],
            Role::class => [
                'backend.roles',
                'v1/backend/views/pages/roles/messages/role-messages-controller.roles.not_found',
            ],
            RoleTranslation::class => [
                'backend.roles.translations',
                'v1/backend/views/pages/roles/messages/role-messages-controller.roles.translations.not_found',
            ],
            Permission::class => [
                'backend.permissions',
                'v1/backend/views/pages/permissions/messages/permission-messages-controller.permissions.not_found',
            ],
            CloudPlatform::class => [
                'backend.cloud-platforms',
                'v1/backend/views/pages/clouds/platforms/messages/cloud-platforms-messages-controller.cloud-platforms.not_found',
            ],
            CloudFolder::class => [
                'backend.cloud-folders',
                'v1/backend/views/pages/clouds/folders/messages/cloud-folders-messages-controller.cloud-folders.not_found',
            ],
            CloudFile::class => [
                'backend.cloud-files',
                'v1/backend/views/pages/clouds/files/messages/cloud-files-messages-controller.cloud-files.not_found',
            ],
            Contact::class => [
                'backend.contacts',
                'v1/backend/views/pages/contacts/messages/contacts-messages-controller.contacts.not_found',
            ],
            Audit::class => [
                'backend.audits',
                'v1/backend/views/pages/audits/messages/audits-messages-controller.audits.not_found',
            ],
            ActivityLog::class => [
                'backend.activity-logs',
                'v1/backend/views/pages/activity-logs/messages/activity-logs-messages-controller.activity_logs.not_found',
            ],
            User::class => [
                'backend.users',
                'v1/backend/views/pages/users/messages/user-messages-controller.users.not_found',
            ],
        ];
    }

    /**
     * Journalise les échecs de résolution de modèle provenant du binding implicite
     * (ModelNotFoundException). Couvre tous les modules migrés vers les routes
     * bindées ({platformVersion}, {geoContinent}, {geoCountry}, {lang}, traductions, etc.).
     *
     * NB : les MNF levés par firstOrFail() dans les méthodes des contrôleurs
     * (ex : restore/force_destroy) sont capturés localement et ne remontent
     * jamais jusqu'ici → aucun double log.
     */
    public function reportModelNotFound(ModelNotFoundException $e): void
    {
        $class = $e->getModel();
        $map = self::modelMap();

        if (! isset($map[$class])) {
            return; // modèle non suivi : on laisse Laravel gérer (404 normal)
        }

        [$actionPrefix, $langKey] = $map[$class];

        $failId = $e->getIds()[0] ?? null;
        $routeName = request()->route()?->getName();
        $action = $routeName !== null ? $routeName.'.binding_not_found' : $actionPrefix.'.binding_not_found';

        $this->log(
            type: 'warning',
            action: $action,
            context: [
                'resource_id' => is_array($failId) ? implode(', ', $failId) : $failId,
                'resource_class' => $class,
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'i18n_params' => ['uuid' => is_array($failId) ? implode(', ', $failId) : (string) $failId],
            ],
            message: $e->getMessage(),
            activity: $langKey,
            isSuccess: false,
        );
    }
}

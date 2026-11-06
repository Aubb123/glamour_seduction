<?php

namespace App\Helpers\Web\Backend;

use App\Enums\Clouds\Platforms\CloudPlatformStatus;
use App\Models\CloudFile;
use App\Models\CloudPlatform;
use App\Models\Lang;
use App\Models\Setting;
use App\Services\Cloud\CloudStorageService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BackendHelper
{
    // Function formatDate() qui formate une date selon un format donné, en utilisant Carbon pour la manipulation des dates et en tenant compte de la locale de l'application
    public static function formatDate($date, string $format): string
    {

        // $format = $format ?? 'D MMMM YYYY - HH:mm:ss'; // Format par défaut si aucun format n'est fourni

        // Si la date est nulle, retourne un tiret ou une valeur par défaut
        if (! $date) {
            return '—';
        }

        // Formate la date en utilisant Carbon et la locale de l'application, puis convertit en majuscules pour les mois
        $formattedDate = mb_convert_case(Carbon::parse($date)->locale(app()->getLocale())->isoFormat($format), MB_CASE_TITLE, 'UTF-8');

        return $formattedDate;
    }

    // Fonction getActiveLangs() qui récupère les langues actives sur la plateforme et la langue courante en fonction de la locale de l'application, et retourne un tableau contenant les langues actives et la langue courante
    public static function getActiveLangs()
    {
        $langs = Lang::where('is_active', true)->get();
        $currentLang = $langs->firstWhere('code', app()->getLocale()) ?? Lang::where('is_active', true)->first(); // Si la langue courante n'est pas trouvée, prend la langue par défaut

        if (! $currentLang) {
            dd('Aucune langue active trouvée. Veuillez vérifier la configuration des langues dans la base de données.');
        }

        return [
            'langs' => $langs,
            'currentLang' => $currentLang,
        ];
    }

    // Function qui permet de supprimer tout ancien fichier cloud associé à une entité avant d'uploader un nouveau fichier, en utilisant le service CloudStorageService pour la suppression
    public static function deleteOldCloudFiles($entity, $fileType)
    {
        $oldCloudFiles = $entity->cloudFiles()->where('file_type', $fileType)->get();
        if ($oldCloudFiles) {
            foreach ($oldCloudFiles as $oldCloudFile) {

                // // 1. Suppression temporaire de l'enregistrement en base
                // $oldCloudFile->delete();

                // // 2. Rafraîchissement du cloudFile pour s'assurer qu'il est bien supprimé de la relation avec l'entité
                // $oldCloudFile->refresh();

                // Suppression réelle du fichier sur le cloud (Backblaze B2)
                $deleted = app(CloudStorageService::class)->delete($oldCloudFile);

                // Si la suppression physique échoue, on NE supprime PAS l'enregistrement en base :
                // cela éviterait de créer un objet orphelin sur le cloud (toujours facturé),
                // plus aucun enregistrement ne permettant de le retrouver. On lève une
                // exception pour annuler la transaction de l'appelant et préserver l'existant.
                if (! $deleted) {
                    throw new \RuntimeException(
                        __('v1/backend/others/helpers/backend-helper-messages.delete_old_cloud_files', [
                            'name' => $oldCloudFile->file_name,
                        ])
                    );
                }

                // Suppression définitive de l'enregistrement en base
                $oldCloudFile->forceDelete();
            }
        }
    }

    // ─────────────────────────────────────────────
    // VALIDATIONS GÉNÉRIQUES DES TRADUCTIONS
    // ─────────────────────────────────────────────
    // Ces méthodes sont partagées par tous les modules dotés de traductions traduisibles
    // (pays, continents, ...). Elles s'appuient sur les propriétés portées par le modèle
    // parent : `getTranslationModelName()` (convention du trait HasTranslations) et
    // `translationForeignKey`. Les libellés d'entité (:entity) sont fournis par l'appelant
    // et les messages sont centralisés dans lang/{fr,en}/v1/backend/others/helpers/.

    /**
     * Vérifie — pour une liste de traductions soumises à l'enregistrement d'une entité —
     * qu'aucun nom ni slug n'entre en conflit avec une traduction existante.
     *
     * Deux types de conflits sont détectés :
     *  1. Avec une AUTRE entité (active ou en corbeille) dans la même langue ;
     *  2. Avec la PROPRE traduction de l'entité, placée en corbeille (soft delete) —
     *     on refuse alors de la « restaurer » silencieusement pour conserver l'unicité.
     *
     * Le slug considéré est celui qui serait généré automatiquement à partir du nom saisi
     * (même logique que le trait HasSlug). La corbeille est incluse : un nom/slug
     * soft-deleté reste « réservé » tant qu'il n'est pas supprimé définitivement.
     * En cas de conflits multiples, le conflit actif est priorisé (plus parlant).
     *
     * Les paramètres $article et $demonstrative permettent d'accorder les messages
     * français avec le genre de l'entité ("un/une", "ce/cette/cet").
     *
     * @param  Model  $entity  Modèle parent (GeoCountry, BlogPost, Gallery, ...)
     * @param  array<int, array<string, mixed>>  $translations  Tableau des traductions soumises
     * @param  string  $entityLabel  Libellé traduit de l'entité ("pays", "article", ...)
     * @param  string  $backendLangPath  Chemin de base des messages (ex: '...backend-helper-messages')
     * @param  string  $nameField  Colonne du "nom" ('name', 'title', 'question', 'label', ...)
     * @param  string|null  $slugField  Colonne du slug ('slug') ou null si la table n'en a pas
     * @param  string  $article  Article indéfini devant ":entity" ("un" / "une")
     * @param  string  $demonstrative  Démonstratif devant ":entity" ("ce" / "cet" / "cette")
     *
     * @throws \InvalidArgumentException
     */
    public static function assertTranslationUniqueness($entity, array $translations, string $entityLabel, string $backendLangPath, string $nameField = 'name', ?string $slugField = 'slug'): void
    {
        $translationModel = $entity->getTranslationModelName(); // Récupère le nom du modèle de traduction associé à l'entité Ex: App\Models\GeoCountryTranslation, App\Models\BlogPostTranslation, App\Models\GalleryTranslation, ...
        $foreignKey = $entity->getTranslationForeignKey(); // Récupère le nom de la clé étrangère dans le modèle de traduction Ex: 'country_id', 'post_id', 'gallery_id', ...

        // Pour chaque traduction soumise, on vérifie les conflits de nom/slug
        foreach ($translations as $translation) {

            $langId = $translation['lang_id']; // Récupère l'ID de la langue de la traduction soumise
            // Génère le slug à partir du nom de la traduction soumise, si applicable.
            // On passe par HasSlug::makeSlug() (via le modèle de traduction) pour
            // centraliser la slugification et rester cohérent avec le slug qui sera
            // réellement produit par le trait (cas des emojis, etc.).
            $newSlug = $slugField !== null ? $translationModel::makeSlug((string) $translation[$nameField]) : null;

            // 1. Conflit avec une AUTRE entité dans la même langue (corbeille incluse)
            $conflictingTranslation = $translationModel::withTrashed()
                ->where('lang_id', $langId)
                ->where($foreignKey, '!=', $entity->id)
                ->where(function ($query) use ($translation, $nameField, $slugField, $newSlug) {
                    $query->where($nameField, $translation[$nameField]);

                    if ($slugField !== null && $newSlug !== null) {
                        $query->orWhere($slugField, $newSlug);
                    }
                })
                ->orderByRaw('deleted_at IS NULL DESC')
                ->first();

            // Si une traduction conflictuelle est trouvée, on lève une exception avec un message explicite
            if ($conflictingTranslation) {
                // Détermine la clé de message à utiliser en fonction de l'état de la traduction conflictuelle (active ou corbeille)
                $key = $conflictingTranslation->trashed() ? 'exists_other_trashed' : 'exists_other_active';

                // Lève une exception avec un message explicite, en utilisant les libellés et articles fournis
                throw new \InvalidArgumentException(__("{$backendLangPath}.translation.{$key}", [
                    'name' => $translation[$nameField],
                    'slug' => $newSlug ?? '',
                    'entity' => $entityLabel,
                    'lang' => $conflictingTranslation->lang?->name ?? $conflictingTranslation->lang_id,
                ]));
            }

            // 2. Conflit avec la PROPRE traduction de l'entité, placée en corbeille
            $ownTrashed = $translationModel::withTrashed()
                ->where($foreignKey, $entity->id)
                ->where('lang_id', $langId)
                ->whereNotNull('deleted_at')
                ->first();

            // Si une traduction de l'entité est trouvée dans la corbeille, on lève une exception avec un message explicite
            if ($ownTrashed) {
                // Lève une exception avec un message explicite, en utilisant les libellés et articles fournis
                throw new \InvalidArgumentException(__("{$backendLangPath}.translation.trashed_conflict", [
                    'name' => $translation[$nameField],
                    'slug' => $slugField !== null ? ($ownTrashed->{$slugField} ?? '') : '',
                    'entity' => $entityLabel,
                    'lang' => $ownTrashed->lang?->name ?? $ownTrashed->lang_id,
                ]));
            }
        }
    }

    /**
     * Vérifie qu'une traduction peut être restaurée (restore) sans créer de conflit
     * de nom/slug avec une AUTRE traduction (active ou en corbeille) dans la même langue.
     *
     * NB : la ligne en cours de restauration est exclue de la recherche (elle devient
     * l'utilisatrice du nom/slug dès sa restauration).
     *
     * @param  Model  $translation  Traduction à restaurer
     * @param  string  $entityLabel  Libellé traduit de l'entité parent
     * @param  string  $backendLangPath  Chemin de base des messages
     * @param  string  $nameField  Colonne du "nom" ('name', 'title', 'question', 'label', ...)
     * @param  string|null  $slugField  Colonne du slug ('slug') ou null si la table n'en a pas
     * @param  string  $article  Article indéfini devant ":entity" ("un" / "une")
     *
     * @throws \InvalidArgumentException
     */
    public static function assertTranslationRestore($translation, string $entityLabel, string $backendLangPath, string $nameField = 'name', ?string $slugField = 'slug'): void
    {
        // Vérifie qu'aucune autre traduction (active ou en corbeille) dans la même langue n'entre en conflit avec le nom/slug de la traduction à restaurer
        $conflictingTranslation = $translation::withTrashed()
            ->where('lang_id', $translation->lang_id)
            ->where('id', '!=', $translation->id)
            ->where(function ($query) use ($translation, $nameField, $slugField) {
                $query->where($nameField, $translation->{$nameField});

                if ($slugField !== null) {
                    $query->orWhere($slugField, $translation->{$slugField});
                }
            })
            ->orderByRaw('deleted_at IS NULL DESC')
            ->first();

        // Si une traduction conflictuelle est trouvée, on lève une exception avec un message explicite
        if ($conflictingTranslation) {
            $key = $conflictingTranslation->trashed() ? 'restore_conflict_trashed' : 'restore_conflict_active';

            throw new \InvalidArgumentException(__("{$backendLangPath}.translation.{$key}", [
                'name' => $translation->{$nameField},
                'slug' => $slugField !== null ? ($translation->{$slugField} ?? '') : '',
                'entity' => $entityLabel,
                'lang' => $conflictingTranslation->lang?->name ?? $conflictingTranslation->lang_id,
            ]));
        }
    }

    // ─────────────────────────────────────────────────────────────
    // VERROU DE SÉCURITÉ — SUPPRESSION D'ENTITÉS PARENTES
    // ─────────────────────────────────────────────────────────────
    // Vérifie qu'aucun enfant bloquant n'est rattaché à l'entité avant toute suppression
    // (delete ou force_delete). Les relations à contrôler sont déclarées sur chaque modèle
    // via la propriété `$dependentRelations`. Lève une \InvalidArgumentException sinon.
    //
    // NB : les relations de type pivot (N-N / BelongsToMany) ne sont jamais déclarées ici :
    // par règle métier, elles ne bloquent pas la suppression (elles sont nettoyées par la
    // cascade DB). Seuls les vrais enfants (HasMany / auto-références) bloquent.
    public static function assertNoDependentChildren($entity, bool $withTrashed = false): void
    {
        // Récupère la liste des relations enfants bloquantes déclarée sur le modèle
        $dependentRelations = self::readDependentRelations($entity); // Ex: Sous forme de tableau ['comments', 'replies', 'translations', ...]

        if (empty($dependentRelations)) {
            return;
        }

        // Collecte les relations qui possèdent au moins un enfant
        $foundRelations = [];

        foreach ($dependentRelations as $relation) { // Ex: Pas sous forme de tableau 'comments', 'replies', 'translations', ...
            if (! method_exists($entity, $relation)) {
                continue;
            }

            $query = $entity->{$relation}(); // Ex: $entity->comments(), $entity->replies(), $entity->translations(), ...

            // Compte le nombre d'enfants selon le type de suppression (delete ou force_delete)
            $count = $withTrashed
                ? $query->withTrashed()->count() // force_delete : la cascade DB écraserait même les enfants en corbeille
                : $query->count();                // delete simple : les enfants en corbeille sont déjà "supprimés"

            // Si le nombre d'enfants est supérieur à zéro, on ajoute la relation à la liste des relations trouvées
            if ($count > 0) {
                $foundRelations[] = $relation;
            }
        }

        // Si aucune relation enfant bloquante n'est trouvée, on quitte la fonction
        if (empty($foundRelations)) {
            return;
        }

        // Informations grammaticales et libellés de l'entité parente (par nom court de classe)
        $entityClass = class_basename($entity); // Ex: 'GeoCountry', 'BlogPost', 'Gallery', ...

        // Récupère les libellés de l'entité depuis le fichier de traduction, ou utilise des valeurs par défaut si non trouvés
        $entityInfo = __("v1/backend/others/helpers/backend-helper-messages.deleteGuard.entities.{$entityClass}");

        // Si la traduction n'est pas trouvée, on utilise des valeurs par défaut
        if (! is_array($entityInfo)) {
            $entityInfo = [
                'label' => $entityClass,
                'demonstrative' => 'ce',
                'pronoun' => 'il',
                'participle' => 'lié',
                'object' => 'le',
            ];
        }

        // Libellés pluriels des enfants (jointure « X, Y et Z »).
        // Chaque entité peut déclarer des libellés spécifiques via
        // deleteGuard.relations.{Entité}.{relation} (ex: 'children' =>
        // 'sous-catégories' pour ProductCategory, mais 'sous-dossiers'
        // pour CloudFolder). Repli sur le libellé générique sinon.
        $childrenLabels = collect($foundRelations)
            ->map(fn (string $relation) => self::translateRelationLabel($entityClass, $relation))
            ->values()
            ->all();

        // Construit la liste des enfants sous forme de chaîne lisible (ex: "X, Y et Z")
        if (count($childrenLabels) === 1) {
            $childrenList = $childrenLabels[0]; // Un seul enfant Ex: "X"
        } else {
            $last = array_pop($childrenLabels); // Récupère le dernier élément Ex: "Z"
            $joiner = __('v1/backend/others/helpers/backend-helper-messages.deleteGuard.joiner'); // "et" (fr) / "and" (en)
            $childrenList = implode(', ', $childrenLabels).' '.(is_string($joiner) ? $joiner : 'et').' '.$last; // Concatène les autres éléments avec des virgules et ajoute le connecteur avant le dernier Ex: "X, Y et Z"
        }

        // Nom lisible de l'entité (traduction active → colonnes génériques → UUID en dernier recours)
        $name = $entity->active_translation?->name
            ?? $entity->active_translation?->title
            ?? $entity->active_translation?->label
            ?? $entity->name
            ?? $entity->label
            ?? $entity->code
            ?? $entity->title
            ?? $entity->uuid;

        throw new \InvalidArgumentException(
            __('v1/backend/others/helpers/backend-helper-messages.deleteGuard.message', [
                'demonstrative' => $entityInfo['demonstrative'],
                'entity' => $entityInfo['label'],
                'name' => $name,
                'pronoun' => $entityInfo['pronoun'],
                'participle' => $entityInfo['participle'],
                'children' => $childrenList,
                'object' => $entityInfo['object'],
            ])
        );
    }

    /**
     * Traduit le libellé d'une relation enfant pour l'entité donnée.
     *
     * Cherche d'abord un libellé spécifique à l'entité
     * (deleteGuard.relations.{Entité}.{relation}), puis retombe sur le
     * libellé générique (deleteGuard.relations.{relation}). En dernier
     * recours, la relation est utilisée telle quelle afin d'éviter
     * l'affichage d'une clé de traduction brute dans le message.
     */
    private static function translateRelationLabel(string $entityClass, string $relation): string
    {
        $base = 'v1/backend/others/helpers/backend-helper-messages.deleteGuard.relations';

        // $specific prend la priorité sur $generic, car certaines entités peuvent vouloir
        // des libellés spécifiques pour leurs relations enfants (ex: ProductCategory vs CloudFolder)
        $specific = __("{$base}.{$entityClass}.{$relation}");

        // si la traduction spécifique existe et est différente de la clé brute, on la retourne
        if (is_string($specific) && $specific !== "{$base}.{$entityClass}.{$relation}") {
            return $specific;
        }

        // $generic est utilisé comme fallback si aucune traduction spécifique n'est trouvée
        $generic = __("{$base}.{$relation}");

        return is_string($generic) && $generic !== "{$base}.{$relation}" ? $generic : $relation;
    }

    /**
     * Lecture de la liste des relations enfants bloquantes déclarée sur le modèle.
     *
     * La propriété est volontairement `protected` (convention du codebase, cf.
     * $translationForeignKey, $translationModel, …). Elle doit donc être lue via
     * Reflection : un accès direct `$entity->dependentRelations` depuis une autre
     * classe est considéré "inaccessible" par PHP et bascule sur `Model::__get()`,
     * qui retourne silencieusement null (aucune colonne/relation de ce nom) —
     * ce qui désactiverait complètement le verrou de sécurité.
     */
    private static function readDependentRelations($entity): array
    {
        try {
            $property = new \ReflectionProperty($entity, 'dependentRelations');

            return is_array($value = $property->getValue($entity)) ? $value : [];
        } catch (\ReflectionException $e) {
            return [];
        }
    }

    // Fonction destroy_function() qui supprime une entité et ses relations associées, y compris les traductions et les fichiers cloud, en utilisant la suppression simple (soft delete)
    public static function destroy_function($entity)
    {
        // Verrou de sécurité : bloque si l'entité parente a des enfants liés non supprimés
        // NB : on ne bloque pas si les enfants sont déjà en corbeille, car la cascade ON DELETE CASCADE de la base les écraserait silencieusement (soft delete) — ce qui est acceptable.
        // self!! permet de vérifier les relations enfants déclarées sur le modèle via la propriété `$dependentRelations`.
        self::assertNoDependentChildren($entity, false);

        // Vérification si l'entité a des traductions
        if (method_exists($entity, 'translations')) {
            // Suppression des traductions associées avant de supprimer l'entité principale pour éviter les erreurs de contrainte d'intégrité (foreign key constraint)
            $entity->translations()->delete();
        }

        // Pour les fichiers cloud
        if (method_exists($entity, 'cloudFiles') && ! $entity instanceof CloudFile) {

            // // Suppression temporaire des fichiers cloud associés sur le cloud avant de supprimer les enregistrements en base de données
            // $entity->cloudFiles()->each(function ($cloudFile) {
            //     // Suppression du fichier sur le cloud en utilisant le service CloudStorageService
            //     app(CloudStorageService::class)->delete($cloudFile);
            // });

            // Suppression temporaire des fichiers cloud associés
            $entity->cloudFiles()->delete();

        } elseif ($entity instanceof CloudFile) {

            // Choix assumé : le soft-delete d'un fichier cloud conserve intentionnellement
            // le fichier physique (local ou B2). Cela permet de le restaurer depuis la corbeille
            // sans perdre les données. La suppression effective du fichier sur le stockage
            // (local et/ou cloud) n'a lieu que lors de la suppression définitive,
            // via force_destroy_function().

        }

        // Suppression simple (soft delete) de l'entité principale elle peut etre un CloudFile ou une autre entité
        $entity->delete();

        return true; // Indique que la suppression a été effectuée avec succès

    }

    public static function restore_function($entity)
    {
        // Restauration de l'entité principale
        $entity->restore();

        // Vérification si l'entité a des traductions
        if (method_exists($entity, 'translations')) {
            // Restauration des traductions associées
            $entity->translations()->withTrashed()->restore();
        }

        // Pour les fichiers cloud, différent de $cloudFile, on restaure les fichiers cloud associés
        if (method_exists($entity, 'cloudFiles') && ! $entity instanceof CloudFile) {
            // Restauration des fichiers cloud associés
            $entity->cloudFiles()->withTrashed()->restore();
        } elseif ($entity instanceof CloudFile) {
            // // Si l'entité est elle-même un CloudFile, on restaure directement le fichier cloud déjà supprimé
            // // Entité CloudFile restaurée, un peu plus haut, on a déjà fait $entity->restore(); donc pas besoin de le refaire ici
            // $entity->restore();
        }

        return true; // Indique que la restauration a été effectuée avec succès
    }

    // Fonction force_destroy_function() qui supprime définitivement une entité et ses relations associées, y compris les traductions et les fichiers cloud,
    // en utilisant la suppression définitive (forceDelete)
    public static function force_destroy_function($entity)
    {
        // Verrou de sécurité : bloque si l'entité parente a des enfants liés (même en corbeille,
        // car la cascade ON DELETE CASCADE de la base les écraserait définitivement)
        self::assertNoDependentChildren($entity, true);

        // Vérification si l'entité a des traductions
        if (method_exists($entity, 'translations')) {
            // Suppression définitive des traductions associées
            $entity->translations()->withTrashed()->forceDelete();
        }

        $cloudFile = $entity;

        // Pour les fichiers cloud
        if (method_exists($entity, 'cloudFiles') && ! $entity instanceof CloudFile) {

            // Suppression définitive des fichiers cloud associés sur le cloud avant de supprimer les enregistrements en base de données
            $entity->cloudFiles()->withTrashed()->each(function ($cloudFile) {
                // Suppression du fichier sur le cloud en utilisant le service CloudStorageService
                app(CloudStorageService::class)->delete($cloudFile);
            });

            // Suppression définitive des fichiers cloud associés
            $entity->cloudFiles()->withTrashed()->forceDelete();

        } elseif ($entity instanceof CloudFile) {

            // Si l'entité est elle-même un CloudFile, on supprime directement le fichier sur le cloud
            app(CloudStorageService::class)->delete($entity);

            // Suppression définitive du fichier cloud elle peut etre un CloudFile ou une autre entité
            // $entity->forceDelete();

        }

        // Suppression définitive de l'entité principale
        $entity->forceDelete();

        return true; // Indique que la suppression définitive a été effectuée avec succès
    }

    // fonction getArchitectureCloud() architecture cloud d'une plateforme cloud donnée
    public static function getArchitectureCloud()
    {
        // L'architecture est externalisée dans config/cloud-architecture.php
        return [
            'architecture' => config('cloud-architecture.architecture', []),
            'foldersToUpdate' => config('cloud-architecture.folders', []),
        ];
    }

    public static function getActiveCloudPlatforms()
    {
        $cloudPlatforms = CloudPlatform::where('status', CloudPlatformStatus::Active)->get();

        return $cloudPlatforms;
    }

    /**
     * Retourne la configuration complète du menu de la sidebar backend.
     */
    public static function getSidebarMenu(): array
    {
        $sidebarMenu = [
            [
                'header' => 'Main',
                'items' => [
                    [
                        'label' => 'Cloud',
                        'icon' => 'ti ti-cloud',
                        'children' => [
                            ['label' => 'Plateformes', 'route' => 'backend.cloud-platforms.index', 'patterns' => ['backend.cloud-platforms.*']],
                            ['label' => 'Dossiers', 'route' => 'backend.cloud-folders.index', 'patterns' => ['backend.cloud-folders.*']],
                            ['label' => 'Fichiers', 'route' => 'backend.cloud-files.index', 'patterns' => ['backend.cloud-files.*']],
                        ],
                    ],
                    [
                        'label' => 'Géographie',
                        'icon' => 'ti ti-map',
                        'children' => [
                            ['label' => 'Continents', 'route' => 'backend.geocontinents.index', 'patterns' => ['backend.geocontinents.*']],
                            ['label' => 'Pays', 'route' => 'backend.geocountries.index', 'patterns' => ['backend.geocountries.*']],
                            ['label' => 'Devises', 'route' => 'backend.currencies.index', 'patterns' => ['backend.currencies.*', 'backend.currency-translations.*']],
                        ],
                    ],
                    [
                        'label' => 'Dashboard',
                        'icon' => 'ti ti-layout-grid',
                        'children' => [
                            ['label' => 'Audits', 'route' => 'backend.audits.index', 'patterns' => ['backend.audits.*']],
                            ['label' => 'Activité', 'route' => 'backend.activity-logs.index', 'patterns' => ['backend.activity-logs.*']],
                            ['label' => 'Paramètres', 'route' => 'backend.settings.index', 'patterns' => ['backend.settings.*']],
                            ['label' => 'Lang',              'route' => 'backend.langs.index',    'patterns' => ['backend.langs.*']],
                            ['label' => 'Plateforme version', 'route' => 'backend.versions.index', 'patterns' => ['backend.versions.*', 'backend.platform-version-translations.*']],
                            ['label' => 'Roles', 'route' => 'backend.roles.index',    'patterns' => ['backend.roles.*']],
                            ['label' => 'Permissions', 'route' => 'backend.permissions.index', 'patterns' => ['backend.permissions.*']],
                            ['label' => 'Utilisateurs', 'route' => 'backend.users.index', 'patterns' => ['backend.users.*']],
                            ['label' => 'Admin Dashboard',   'route' => 'backend.index',           'patterns' => ['backend.index']],
                            ['label' => 'Analytics Dashboard', 'route' => 'backend.analytics',         'patterns' => ['backend.analytics']],
                        ],
                    ],
                    [
                        'label' => 'Boutique',
                        'icon' => 'ti ti-shopping-cart',
                        'children' => [
                            ['label' => 'Catégories', 'route' => 'backend.product-categories.index', 'patterns' => ['backend.product-categories.*', 'backend.product-category-translations.*']],
                            ['label' => 'Marques', 'route' => 'backend.product-brands.index', 'patterns' => ['backend.product-brands.*', 'backend.product-brand-translations.*']],
                            ['label' => 'Tags', 'route' => 'backend.product-tags.index', 'patterns' => ['backend.product-tags.*', 'backend.product-tag-translations.*']],
                            ['label' => 'Attributs', 'route' => 'backend.product-attributes.index', 'patterns' => ['backend.product-attributes.*', 'backend.product-attribute-translations.*']],
                            ['label' => "Valeurs d'attributs", 'route' => 'backend.product-attribute-values.index', 'patterns' => ['backend.product-attribute-values.*', 'backend.product-attribute-value-translations.*']],
                            ['label' => 'Produits', 'route' => 'backend.products.index', 'patterns' => ['backend.products.*', 'backend.product-translations.*']],
                            ['label' => 'Variantes', 'route' => 'backend.product-variants.index', 'patterns' => ['backend.product-variants.*']],
                            ['label' => 'Entrepôts', 'route' => 'backend.product-warehouses.index', 'patterns' => ['backend.product-warehouses.*']],
                            ['label' => 'Stocks', 'route' => 'backend.product-warehouse-stocks.index', 'patterns' => ['backend.product-warehouse-stocks.*']],
                            ['label' => 'Mouvements de stock', 'route' => 'backend.product-stock-movements.index', 'patterns' => ['backend.product-stock-movements.*']],
                            ['label' => 'Boutiques', 'route' => 'backend.shops.index', 'patterns' => ['backend.shops.*', 'backend.shop-translations.*']],
                        ],
                    ],
                    [
                        'label' => 'Blog',
                        'icon' => 'ti ti-brand-blogger',
                        'children' => [
                            ['label' => 'Catégories', 'route' => 'backend.blog-categories.index', 'patterns' => ['backend.blog-categories.*', 'backend.blog-category-translations.*']],
                            ['label' => 'Tags', 'route' => 'backend.blog-tags.index', 'patterns' => ['backend.blog-tags.*', 'backend.blog-tags.trashed', 'backend.blog-tag-translations.*']],
                            ['label' => 'Articles', 'route' => 'backend.blog-posts.index', 'patterns' => ['backend.blog-posts.*', 'backend.blog-post-translations.*']],
                            ['label' => 'Commentaires', 'route' => 'backend.blog-post-comments.index', 'patterns' => ['backend.blog-post-comments.*']],
                            ['label' => 'Réponses', 'route' => 'backend.blog-post-comment-replies.index', 'patterns' => ['backend.blog-post-comment-replies.*']],
                        ],
                    ],
                    [
                        'label' => 'Galeries',
                        'icon' => 'ti ti-photo',
                        'children' => [
                            ['label' => 'Galeries', 'route' => 'backend.galleries.index', 'patterns' => ['backend.galleries.*', 'backend.gallery-translations.*']],
                        ],
                    ],
                    [
                        'label' => 'FAQ',
                        'icon' => 'ti ti-question-mark',
                        'children' => [
                            ['label' => 'FAQs', 'route' => 'backend.faqs.index', 'patterns' => ['backend.faqs.*', 'backend.faq-translations.*']],
                        ],
                    ],
                    [
                        'label' => 'Newsletter',
                        'icon' => 'ti ti-mail',
                        'children' => [
                            ['label' => 'Abonnés', 'route' => 'backend.subscribes.index', 'patterns' => ['backend.subscribes.*']],
                            ['label' => 'Historique', 'route' => 'backend.subscribe-histories.index', 'patterns' => ['backend.subscribe-histories.*']],
                        ],
                    ],
                    [
                        'label' => 'Contacts',
                        'icon' => 'ti ti-message-circle',
                        'children' => [
                            ['label' => 'Messages', 'route' => 'backend.contacts.index', 'patterns' => ['backend.contacts.*']],
                        ],
                    ],
                ],
            ],
            [
                'header' => 'Inventory',
                'items' => [
                    ['label' => 'Products', 'icon' => 'box', 'route' => 'backend.index', 'feather' => true],
                ],
            ],
        ];

        return $sidebarMenu;

    }

    /**
     * Retourne les liens de création pour les entités du backend, avec leurs labels, icônes, routes et permissions associées.
     */
    public static function getCreateLinks(): array
    {
        $createLinks = [
            [
                'label' => __('Version'),
                'icon' => 'ti ti-code',
                'route' => 'backend.versions.create',
                'permission' => 'platform-versions.create',
            ],
            [
                'label' => __('Permission'),
                'icon' => 'ti ti-lock',
                'route' => 'backend.permissions.create',
                'permission' => 'permissions.create',
            ],
            [
                'label' => __('Catégorie'),
                'icon' => 'ti ti-folder',
                'route' => 'backend.blog-categories.create',
                'permission' => 'blog-categories.create',
            ],
            [
                'label' => __('Tag'),
                'icon' => 'ti ti-tag',
                'route' => 'backend.blog-tags.create',
                'permission' => 'blog-tags.create',
            ],
            [
                'label' => __('Article'),
                'icon' => 'ti ti-file-text',
                'route' => 'backend.blog-posts.create',
                'permission' => 'blog-posts.create',
            ],
            [
                'label' => __('Galerie'),
                'icon' => 'ti ti-photo',
                'route' => 'backend.galleries.create',
                'permission' => 'galleries.create',
            ],
            [
                'label' => __('FAQ'),
                'icon' => 'ti ti-help',
                'route' => 'backend.faqs.create',
                'permission' => 'faqs.create',
            ],
            [
                'label' => __('Catégorie produit'),
                'icon' => 'ti ti-category',
                'route' => 'backend.product-categories.create',
                'permission' => 'product-categories.create',
            ],
            [
                'label' => __('Marque'),
                'icon' => 'ti ti-tag',
                'route' => 'backend.product-brands.create',
                'permission' => 'product-brands.create',
            ],
            [
                'label' => __('Tag produit'),
                'icon' => 'ti ti-bookmark',
                'route' => 'backend.product-tags.create',
                'permission' => 'product-tags.create',
            ],
            [
                'label' => __('Attribut produit'),
                'icon' => 'ti ti-adjustments',
                'route' => 'backend.product-attributes.create',
                'permission' => 'product-attributes.create',
            ],
            [
                'label' => __('Produit'),
                'icon' => 'ti ti-box',
                'route' => 'backend.products.create',
                'permission' => 'products.create',
            ],
            [
                'label' => __('Entrepôt'),
                'icon' => 'ti ti-building-warehouse',
                'route' => 'backend.product-warehouses.create',
                'permission' => 'product-warehouses.create',
            ],
            [
                'label' => __('Stock'),
                'icon' => 'ti ti-packages',
                'route' => 'backend.product-warehouse-stocks.create',
                'permission' => 'product-warehouse-stocks.create',
            ],
            [
                'label' => __('Mouvement de stock'),
                'icon' => 'ti ti-arrows-exchange',
                'route' => 'backend.product-stock-movements.create',
                'permission' => 'product-stock-movements.create',
            ],
            [
                'label' => 'Boutique', 
                'route' => 'backend.shops.create', 
                'permission' => 'shops.create'
            ],
            [
                'label' => __("Valeur d'attribut"),
                'icon' => 'ti ti-list-details',
                'route' => 'backend.product-attribute-values.create',
                'permission' => 'product-attribute-values.create',
            ],
            [
                'label' => __('Cloud'),
                'icon' => 'ti ti-cloud',
                'route' => 'backend.cloud-platforms.create',
                'permission' => 'cloud-platforms.create',
            ],
        ];

        return $createLinks;
    }

    /**
     * Vérifie si la route courante correspond à un pattern donné.
     */
    public static function isRouteActive(array $patterns): bool
    {
        return request()->routeIs(...$patterns);
    }

    /**
     * Vérifie si au moins un enfant du menu a une route active.
     */
    public static function isChildRouteActive(array $children): bool
    {
        return collect($children)->contains(function ($child) {
            $patterns = $child['patterns'] ?? [$child['route']];

            return request()->routeIs(...$patterns);
        });
    }

    /**
     * Récupère la valeur d'un paramètre de configuration.
     */
    public static function getSettingValue(string $key, ?string $locale = null): mixed
    {
        // Récupère le nombre maximum de fichiers cloudfiles non synchronisés avant redirection
        $setting_max_unsynced_files_before_redirect = Setting::where('key', 'max_unsynced_files_before_redirect')->first();

        // Récupère le nombres maximum de fichiers cloudfiles à synchroniser en une seule fois
        $setting_max_files_sync_batch = Setting::where('key', 'max_files_sync_batch')->first();

        // Récupère le délai de réessai après un échec de synchronisation
        $setting_retry_sync_after_minutes = Setting::where('key', 'retry_sync_after_minutes')->first();

        // Récupère le délai avant remise en file des fichiers bloqués en "processing"
        $setting_reset_stuck_processing_after_minutes = Setting::where('key', 'reset_stuck_processing_after_minutes')->first();

        // Récupère la durée de validité des URL temporaires
        $setting_temporary_url_expires_in_minutes = Setting::where('key', 'temporary_url_expires_in_minutes')->first();

        // Récupère le nombre maximum de pays insérés par seed
        $setting_max_countries_per_seed = Setting::where('key', 'max_countries_per_seed')->first();

        // Récupère le nom du site
        $setting_site_name = Setting::where('key', 'site_name')->first();

        // Récupère le mode maintenance du site
        $setting_maintenance_mode = Setting::where('key', 'maintenance_mode')->first();

        return match ($key) {
            'max_unsynced_files_before_redirect' => $setting_max_unsynced_files_before_redirect?->getValue($locale),
            'max_files_sync_batch' => $setting_max_files_sync_batch?->getValue($locale),
            'retry_sync_after_minutes' => $setting_retry_sync_after_minutes?->getValue($locale),
            'reset_stuck_processing_after_minutes' => $setting_reset_stuck_processing_after_minutes?->getValue($locale),
            'temporary_url_expires_in_minutes' => $setting_temporary_url_expires_in_minutes?->getValue($locale),
            'max_countries_per_seed' => $setting_max_countries_per_seed?->getValue($locale),
            'site_name' => $setting_site_name?->getValue($locale),
            'maintenance_mode' => $setting_maintenance_mode?->getValue($locale),
            default => null,
        };

    }
}

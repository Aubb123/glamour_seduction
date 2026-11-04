<?php

namespace App\Services\V1\Controllers;

use App\Enums\PlatformVersions\PlatformVersionStatus;
use App\Helpers\Web\Backend\BackendHelper;
use App\Models\PlatformVersion;
use App\Models\PlatformVersionTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class PlatformVersionService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/platform-versions/messages/platform-messages-service.versions';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_versions_store(array $data): PlatformVersion
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Création de la version (sauf les traductions)
            $version = PlatformVersion::create([
                'code' => $data['code'],
                'status' => ($data['status'] ?? null) === PlatformVersionStatus::Active->value
                    ? PlatformVersionStatus::Active
                    : PlatformVersionStatus::Inactive,
                'is_published' => $data['is_published'] ?? false,
                'is_default' => $data['is_default'] ?? false,
                'released_at' => $data['released_at'] ?? null,
            ]);

            // Ensuite, synchronisation des traductions (création ou mise à jour)
            $this->syncTranslations($version, $data['translations']);

            return $version; // ← retourne la version créée avec ses traductions
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_versions_update(PlatformVersion $version, array $data): PlatformVersion
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($version, $data) {

            // Si $version->is_published est true et que 'is_published' n'est pas défini à true dans $data, on fait un redirect back avec un message d'erreur car une version publiée ne peut pas être dépubliée
            if ($version->is_published && ! ($data['is_published'] ?? false)) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.unpublish_forbidden"));
            }

            // Si 'is_published' est définie à true et que la version n'est pas encore publiée, on met à jour 'released_at' à la date actuelle si elle n'est pas déjà définie
            if (($data['is_published'] ?? false) && ! $version->is_published) {
                $data['released_at'] = $version->released_at ?? getDateTime();
            }

            // Protection : une version publiée qui possède déjà une date de lancement
            // ne peut plus la perdre : released_at ne peut plus être remis à null.
            if ($version->is_published && $version->released_at && blank($data['released_at'])) {
                $data['released_at'] = $version->released_at;
            }

            // Mise à jour des champs de la version (sauf les traductions)
            $version->update([
                'code' => $data['code'],
                'status' => ($data['status'] ?? null) === PlatformVersionStatus::Active->value
                    ? PlatformVersionStatus::Active
                    : PlatformVersionStatus::Inactive,
                'is_published' => $data['is_published'] ?? false,
                'is_default' => $data['is_default'] ?? false,
                'released_at' => $data['released_at'] ?? null,
            ]);

            // Ensuite, synchronisation des traductions (création ou mise à jour)
            $this->syncTranslations($version, $data['translations']);

            return $version; // ← retourne la version mise à jour avec ses traductions
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_versions_destroy(PlatformVersion $version): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($version) {

            // Si la version est publiée, on fait un redirect back avec un message d'erreur car une version publiée ne peut pas être supprimée
            if ($version->is_published) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.published_forbidden"));
            }

            // Si la version est active, on fait un redirect back avec un message d'erreur car une version active ne peut pas être supprimée
            if ($version->status === PlatformVersionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }

            // Suppression simple (deleted_at)
            if ($version->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }
            destroy_function($version); // ← suppression de la version et de ses traductions associées + gestion de suppression des fichiers associés si besoin

        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_versions_activate(PlatformVersion $version): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($version) {

            // Vérification que la version n'est pas déjà active
            if ($version->status === PlatformVersionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            // Seules les versions publiées peuvent être activées (cohérence avec VersionService::getActive)
            if (! $version->is_published) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.published_required"));
            }

            // Choix assumé : le multi-actif est autorisé. Plusieurs versions peuvent donc
            // être actives en même temps : on ne désactive PAS les autres versions ici.
            // La version réellement exposée côté front est résolue par VersionService::getActive()
            // (status = active ET is_published = true ET released_at non null).

            // Activation de la version sélectionnée
            $version->update([
                'status' => PlatformVersionStatus::Active,
            ]);

        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_versions_deactivate(PlatformVersion $version): void
    {

        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($version) {

            // Vérification que la version n'est pas déjà inactive
            if ($version->status !== PlatformVersionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            // Désactivation de la version sélectionnée
            $version->update([
                'status' => PlatformVersionStatus::Inactive,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // PUBLISH
    // ─────────────────────────────────────────────
    public function backend_versions_publish(PlatformVersion $version): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($version) {

            // Vérification que la version n'est pas déjà publiée
            if ($version->is_published) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.publish.already_published"));
            }

            // Publication de la version sélectionnée (mise à jour de released_at si pas déjà défini)
            $version->update([
                'is_published' => true,
                'released_at' => $version->released_at ?? getDateTime(),
            ]);

        });
    }

    // ─────────────────────────────────────────────
    // UNPUBLISH
    // ─────────────────────────────────────────────
    public function backend_versions_unpublish(PlatformVersion $version): void
    {

        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($version) {

            // ⛔ Verrouillage métier : une version publiée ne peut jamais être dépubliée.
            // Une fois publiée, is_published reste vrai (historique de publication conservé).
            // Pour la retirer du front, il faut la DÉSACTIVER via backend_versions_deactivate() → status = inactive.
            if ($version->is_published) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.unpublish.published_forbidden"));
            }

            // Une version non publiée ne peut pas être "dépubliée" non plus : elle est déjà dépubliée.
            if (! $version->is_published) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.unpublish.already_unpublished"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_versions_restore(PlatformVersion $version): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($version) {

            // Restauration simple (restore)
            if ($version->trashed()) {

                // Check métier : le code ne doit pas être occupé par une autre version
                // non supprimée, sinon la restauration violerait la contrainte unique sur code.
                $codeTaken = PlatformVersion::where('code', $version->code)
                    ->whereNull('deleted_at')
                    ->where('id', '!=', $version->id)
                    ->exists();

                if ($codeTaken) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.code_conflict"));
                }

                restore_function($version); // <- restauration de la version sélectionnée et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DELETE
    // ─────────────────────────────────────────────
    public function backend_versions_force_destroy(PlatformVersion $version): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($version) {

            // Suppression définitive (forceDelete)
            if ($version->trashed()) {
                force_destroy_function($version); // <- suppression définitive de la version sélectionnée et de ses traductions associées + gestion de suppression des fichiers associés si besoin
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(PlatformVersion $version, array $translations): void
    {

        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime les traductions qui ne sont pas présentes dans les données soumises
        PlatformVersionTranslation::where('platform_version_id', $version->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        PlatformVersionTranslation::onlyTrashed()->where('platform_version_id', $version->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre version (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de la version, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        BackendHelper::assertTranslationUniqueness(
            $version,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.version'),
            $this->backendHelperLangPath,
            'name',
            'slug'
        );

        // Pour chaque traduction reçue, on utilise updateOrCreate pour créer ou mettre à jour la traduction correspondante
        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour la version et la langue
            $existingTranslation = PlatformVersionTranslation::withTrashed()
                ->where('platform_version_id', $version->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->backendLangPath}.trashedTranslationConflict", [
                            'lang_id' => $translation['lang_id'],
                        ])
                    );
                }

                // Met à jour la traduction existante.
                // NB : le slug n'est PAS renseigné ici — le trait HasSlug le régénère
                // automatiquement (avec garantie d'unicité par langue) si le nom change.
                $existingTranslation->update([
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            } else {
                // Sinon, crée une nouvelle traduction
                PlatformVersionTranslation::create([
                    'platform_version_id' => $version->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}

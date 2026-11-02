<?php

namespace App\Services\V1\Controllers;

use App\Enums\Geos\Continents\GeoContinentStatus;
use App\Models\GeoContinent;
use App\Models\GeoContinentTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class GeoContinentService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/geos/continents/messages/continent-messages-service.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(GeoContinent $continent, array $data): GeoContinent
    {
        // Transaction justifiée ici : plusieurs écritures (continent + traductions)
        DB::transaction(function () use ($continent, $data) {

            // Mise à jour des champs du continent
            $continent->update([
                'code' => $data['code'],
                'population' => $data['population'] ?? null,
                'area_km2' => $data['area_km2'] ?? null,
                'sort_order' => $data['sort_order'],
                'status' => ($data['status'] ?? null) === GeoContinentStatus::Active->value
                    ? GeoContinentStatus::Active
                    : GeoContinentStatus::Inactive,
            ]);

            // Mise à jour ou création des traductions
            $this->syncTranslations($continent, $data['translations'] ?? []);

        });

        return $continent->fresh()->load('translations');
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function activate(GeoContinent $continent): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($continent) {

            // Activation du continent sélectionné
            $continent->update([
                'status' => GeoContinentStatus::Active,
            ]);

        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function deactivate(GeoContinent $continent): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($continent) {

            // Désactivation du continent sélectionné
            $continent->update(['status' => GeoContinentStatus::Inactive]);

        });
    }

    // ─────────────────────────────────────────────
    // Synchronisation des traductions
    // ─────────────────────────────────────────────
    private function syncTranslations(GeoContinent $continent, array $translations): void
    {

        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime les traductions qui ne sont pas présentes dans les données soumises
        GeoContinentTranslation::where('continent_id', $continent->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        GeoContinentTranslation::onlyTrashed()->where('continent_id', $continent->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de l'entité, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $continent,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.continent'),
            $this->helperLangPath
        );

        // Pour chaque traduction reçue, on utilise updateOrCreate pour créer ou mettre à jour la traduction correspondante
        foreach ($translations as $translation) {

            $langId = $translation['lang_id'];

            // Vérifie si une traduction existe déjà pour le continent et la langue
            $existingTranslation = GeoContinentTranslation::withTrashed()
                ->where('continent_id', $continent->id)
                ->where('lang_id', $langId)
                ->first();

            // Si la traduction existe déjà, on met à jour les champs
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __('v1/backend/others/helpers/backend-helper-messages.trashedTranslationConflict', [
                            'lang_id' => $langId,
                        ])
                    );
                }

                $existingTranslation->update([
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);

            } else {
                // Sinon, on crée une nouvelle traduction
                GeoContinentTranslation::create([
                    'continent_id' => $continent->id,
                    'lang_id' => $langId,
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }
    }
}

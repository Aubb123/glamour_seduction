<?php

namespace App\Services\V1\Controllers;

use App\Enums\Geos\Countries\GeoCountryStatus;
use App\Models\GeoCountry;
use App\Models\GeoCountryTranslation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

use function __;

class GeoCountryService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/geos/countries/messages/countries-messages-service.countries';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(GeoCountry $country, array $data): GeoCountry
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($country, $data) {

            $attributes = [];

            // Champs obligatoires : toujours présents dans les données validées
            foreach (['continent_id', 'type', 'code_2', 'sort_order'] as $requiredField) {
                $attributes[$requiredField] = $data[$requiredField];
            }

            // Champs nullables : on distingue « champ absent du payload » (on conserve
            // la valeur existante) et « champ présent avec null » (on VIDE le champ).
            // C'est ce qui permet par exemple de détacher un territoire de son pays
            // souverain ou de retirer la devise d'un pays.
            foreach (['sovereign_id', 'code', 'phone_code', 'currency_id'] as $nullableField) {
                $attributes[$nullableField] = Arr::exists($data, $nullableField)
                    ? $data[$nullableField] // ← null explicite = on vide le champ
                    : $country->{$nullableField}; // ← absent = on conserve l'existant
            }

            // file_path est NOT NULL en base : conserve l'image existante si non fournie
            $attributes['file_path'] = $data['file_path'] ?? $country->file_path;

            // Statut : la case à cocher est absente du payload = pays masqué (convention formulaire)
            $attributes['status'] = ($data['status'] ?? null) === GeoCountryStatus::Active->value ? GeoCountryStatus::Active : GeoCountryStatus::Inactive;

            // Mise à jour des champs du pays (sauf les traductions)
            $country->update($attributes);

            // Ensuite, synchronisation des traductions (création ou mise à jour)
            $this->syncTranslations($country, $data['translations']);

            return $country; // ← retourne le pays mis à jour avec ses traductions
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function activate(GeoCountry $country): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($country) {

            // Si le pays est déjà actif, on lève une exception métier pour éviter une opération inutile
            if ($country->status === GeoCountryStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.activate.already_active"));
            }

            // Activation du pays sélectionné
            $country->update([
                'status' => GeoCountryStatus::Active,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function deactivate(GeoCountry $country): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($country) {

            // Si le pays est déjà inactif, on lève une exception métier pour éviter une opération inutile
            if ($country->status === GeoCountryStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->langPath}.deactivate.already_inactive"));
            }

            // Désactivation du pays sélectionné
            $country->update([
                'status' => GeoCountryStatus::Inactive,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(GeoCountry $country, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime les traductions qui ne sont plus présentes dans le formulaire
        GeoCountryTranslation::where('country_id', $country->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        GeoCountryTranslation::onlyTrashed()->where('country_id', $country->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de l'entité, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $country,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.country'),
            $this->helperLangPath
        );

        // Pour chaque traduction reçue, on utilise updateOrCreate pour créer ou mettre à jour la traduction correspondante
        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour le pays et la langue
            $existingTranslation = GeoCountryTranslation::withTrashed()
                ->where('country_id', $country->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->langPath}.trashedTranslationConflict", [
                            'lang_id' => $translation['lang_id'],
                        ])
                    );
                }

                // Si la traduction existe déjà, on la met à jour avec les nouvelles données.
                // NB : le slug n'est PAS défini manuellement ici — le trait HasSlug le régénère
                // automatiquement (avec garantie d'unicité par langue) si le nom change.
                $existingTranslation->update([
                    'name' => $translation['name'],
                    // La description est toujours fournie (règle "required" du FormRequest) — jamais null.
                    'description' => $translation['description'],
                ]);

            } else {
                // Si la traduction n'existe pas, on la crée
                GeoCountryTranslation::create([
                    'country_id' => $country->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    // La description est toujours fournie (règle "required" du FormRequest) — jamais null.
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}

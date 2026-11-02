<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Attributes\ProductAttributeValueStatus;
use App\Models\ProductAttributeValue;
use App\Models\ProductAttributeValueTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductAttributeValueService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/attribute-values/messages/attribute-values-messages-service.productAttributeValues';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(array $data): ProductAttributeValue
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Création de la valeur (sauf les traductions)
            $valeur = ProductAttributeValue::create([
                'attribute_id' => $data['attribute_id'],
                'value' => $data['value'],
                'sort_order' => $data['sort_order'],
                'status' => ($data['status'] ?? null) === ProductAttributeValueStatus::Active->value ? ProductAttributeValueStatus::Active : ProductAttributeValueStatus::Inactive,
            ]);

            // Synchronisation des traductions (création ou mise à jour)
            $this->syncTranslations($valeur, $data['translations']);

            return $valeur; // ← retourne la valeur créée avec ses traductions
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(ProductAttributeValue $valeur, array $data): ProductAttributeValue
    {
        return DB::transaction(function () use ($valeur, $data) {

            $updateData = [];

            if (isset($data['attribute_id'])) {
                $updateData['attribute_id'] = $data['attribute_id'];
            }

            if (isset($data['value'])) {
                $updateData['value'] = $data['value'];
            }

            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            $updateData['status'] = ($data['status'] ?? null) === ProductAttributeValueStatus::Active->value
                ? ProductAttributeValueStatus::Active
                : ProductAttributeValueStatus::Inactive;

            $valeur->update($updateData);

            if (isset($data['translations'])) {
                $this->syncTranslations($valeur, $data['translations']);
            }

            return $valeur;
        });
    }

    // __APPEND_1__
    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function activate(ProductAttributeValue $valeur): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($valeur) {

            // Vérification que le valeur n'est pas déjà actif
            if ($valeur->status === ProductAttributeValueStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.activate.already_active"));
            }

            // Activation du valeur sélectionné
            $valeur->update([
                'status' => ProductAttributeValueStatus::Active,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function deactivate(ProductAttributeValue $valeur): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($valeur) {

            // Vérification que le valeur n'est pas déjà inactif
            if ($valeur->status === ProductAttributeValueStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->langPath}.deactivate.already_inactive"));
            }

            // Désactivation du valeur sélectionné
            $valeur->update([
                'status' => ProductAttributeValueStatus::Inactive,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(ProductAttributeValue $valeur): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($valeur) {

            // Vérification : empêcher la suppression d'un valeur actif
            if ($valeur->status === ProductAttributeValueStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.active_forbidden"));
            }

            // Suppression simple (soft delete)
            if ($valeur->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($valeur); // ← suppression du valeur et de ses traductions associées

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(ProductAttributeValue $valeur): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($valeur) {

            // Restauration simple (restore)
            if ($valeur->trashed()) {
                restore_function($valeur); // ← restauration du valeur et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(ProductAttributeValue $valeur): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($valeur) {

            // Suppression définitive (forceDelete)
            if ($valeur->trashed()) {
                force_destroy_function($valeur); // ← suppression définitive du valeur et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(ProductAttributeValue $valeur, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions qui ne sont pas présentes dans les données soumises
        ProductAttributeValueTranslation::where('attribute_value_id', $valeur->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        ProductAttributeValueTranslation::onlyTrashed()->where('attribute_value_id', $valeur->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction du valeur, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $valeur,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.productAttributeValue'),
            $this->helperLangPath,
            'label',
            'slug'
        );

        // Pour chaque traduction reçue, on utilise updateOrCreate pour créer ou mettre à jour la traduction correspondante
        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour le valeur et la langue
            $existingTranslation = ProductAttributeValueTranslation::withTrashed()
                ->where('attribute_value_id', $valeur->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée,
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __('v1/backend/others/helpers/backend-helper-messages.trashedTranslationConflict', [
                            'lang_id' => $translation['lang_id'],
                        ])
                    );
                }

                // Met à jour la traduction existante avec les nouvelles données
                $existingTranslation->update([
                    'label' => $translation['label'],
                ]);

            } else {

                // Sinon, on crée une nouvelle traduction
                ProductAttributeValueTranslation::create([
                    'attribute_value_id' => $valeur->id,
                    'lang_id' => $translation['lang_id'],
                    'label' => $translation['label'],
                ]);

            }

        }
    }
}

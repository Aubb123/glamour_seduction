<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Attributes\ProductAttributeStatus;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductAttributeService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/attributes/messages/attributes-messages-service.productAttributes';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(array $data): ProductAttribute
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Création du attribut (sauf les traductions)
            $attribut = ProductAttribute::create([
                'sort_order' => $data['sort_order'],
                'status' => ($data['status'] ?? null) === ProductAttributeStatus::Active->value ? ProductAttributeStatus::Active : ProductAttributeStatus::Inactive,
            ]);

            // Synchronisation des traductions (création ou mise à jour)
            $this->syncTranslations($attribut, $data['translations']);

            return $attribut; // ← retourne le attribut créé avec ses traductions
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(ProductAttribute $attribut, array $data): ProductAttribute
    {
        return DB::transaction(function () use ($attribut, $data) {

            $updateData = [];

            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            $updateData['status'] = ($data['status'] ?? null) === ProductAttributeStatus::Active->value
                ? ProductAttributeStatus::Active
                : ProductAttributeStatus::Inactive;

            $attribut->update($updateData);

            if (isset($data['translations'])) {
                $this->syncTranslations($attribut, $data['translations']);
            }

            return $attribut;
        });
    }

    // __APPEND_1__
    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function activate(ProductAttribute $attribut): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($attribut) {

            // Vérification que le attribut n'est pas déjà actif
            if ($attribut->status === ProductAttributeStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.activate.already_active"));
            }

            // Activation du attribut sélectionné
            $attribut->update([
                'status' => ProductAttributeStatus::Active,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function deactivate(ProductAttribute $attribut): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($attribut) {

            // Vérification que le attribut n'est pas déjà inactif
            if ($attribut->status === ProductAttributeStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->langPath}.deactivate.already_inactive"));
            }

            // Désactivation du attribut sélectionné
            $attribut->update([
                'status' => ProductAttributeStatus::Inactive,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(ProductAttribute $attribut): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($attribut) {

            // Vérification : empêcher la suppression d'un attribut actif
            if ($attribut->status === ProductAttributeStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.active_forbidden"));
            }

            // Suppression simple (soft delete)
            if ($attribut->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($attribut); // ← suppression du attribut et de ses traductions associées

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(ProductAttribute $attribut): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($attribut) {

            // Restauration simple (restore)
            if ($attribut->trashed()) {
                restore_function($attribut); // ← restauration du attribut et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(ProductAttribute $attribut): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($attribut) {

            // Suppression définitive (forceDelete)
            if ($attribut->trashed()) {
                force_destroy_function($attribut); // ← suppression définitive du attribut et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(ProductAttribute $attribut, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions qui ne sont pas présentes dans les données soumises
        ProductAttributeTranslation::where('attribute_id', $attribut->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        ProductAttributeTranslation::onlyTrashed()->where('attribute_id', $attribut->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction du attribut, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $attribut,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.productAttribute'),
            $this->helperLangPath,
            'name',
            'slug'
        );

        // Pour chaque traduction reçue, on utilise updateOrCreate pour créer ou mettre à jour la traduction correspondante
        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour le attribut et la langue
            $existingTranslation = ProductAttributeTranslation::withTrashed()
                ->where('attribute_id', $attribut->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée,
            if ($existingTranslation) {

                // Met à jour la traduction existante avec les nouvelles données
                $existingTranslation->update([
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);

            } else {

                // Sinon, on crée une nouvelle traduction
                ProductAttributeTranslation::create([
                    'attribute_id' => $attribut->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);

            }

        }
    }
}

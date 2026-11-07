<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Attributes\ProductAttributeType;
use App\Enums\Products\Attributes\ProductAttributeValueStatus;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductAttributeValueTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductAttributeValueService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/attribute-values/messages/attribute-values-messages-service.productAttributeValues';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_store(array $data): ProductAttributeValue
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Création de la valeur (sauf les traductions)
            $value = ProductAttributeValue::create([
                'attribute_id' => $data['attribute_id'],
                'value' => $data['value'],
                'hex_color' => $this->normalizeHexColor($data['attribute_id'], $data['hex_color'] ?? null),
                'sort_order' => $data['sort_order'],
                'status' => ($data['status'] ?? null) === ProductAttributeValueStatus::Active->value ? ProductAttributeValueStatus::Active : ProductAttributeValueStatus::Inactive,
            ]);

            // Synchronisation des traductions (création ou mise à jour)
            $this->syncTranslations($value, $data['translations']);

            return $value; // ← retourne la valeur créée avec ses traductions
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_update(ProductAttributeValue $value, array $data): ProductAttributeValue
    {
        return DB::transaction(function () use ($value, $data) {

            $updateData = [];

            if (isset($data['attribute_id'])) {
                $updateData['attribute_id'] = $data['attribute_id'];
            }

            if (isset($data['value'])) {
                $updateData['value'] = $data['value'];
            }

            // Code couleur HEX : normalisé et gardé uniquement si l'attribut cible est de type "color".
            // L'attribut de référence est celui soumis (recalcul après un éventuel changement d'attribut).
            $targetAttributeId = $data['attribute_id'] ?? $value->attribute_id;
            $updateData['hex_color'] = $this->normalizeHexColor($targetAttributeId, $data['hex_color'] ?? null);

            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            $updateData['status'] = ($data['status'] ?? null) === ProductAttributeValueStatus::Active->value
                ? ProductAttributeValueStatus::Active
                : ProductAttributeValueStatus::Inactive;

            $value->update($updateData);

            if (isset($data['translations'])) {
                $this->syncTranslations($value, $data['translations']);
            }

            return $value;
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_activate(ProductAttributeValue $value): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($value) {

            // Vérification que la valeur n'est pas déjà active
            if ($value->status === ProductAttributeValueStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            // Activation de la valeur sélectionnée
            $value->update([
                'status' => ProductAttributeValueStatus::Active,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_deactivate(ProductAttributeValue $value): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($value) {

            // Vérification que la valeur n'est pas déjà inactive
            if ($value->status === ProductAttributeValueStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            // Désactivation de la valeur sélectionnée
            $value->update([
                'status' => ProductAttributeValueStatus::Inactive,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_destroy(ProductAttributeValue $value): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($value) {

            // Vérification : empêcher la suppression d'une valeur active
            if ($value->status === ProductAttributeValueStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }

            // Suppression simple (soft delete)
            if ($value->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }
            destroy_function($value); // ← suppression de la valeur et de ses traductions associées

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_restore(ProductAttributeValue $value): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($value) {

            // Restauration simple (restore)
            if ($value->trashed()) {
                restore_function($value); // ← restauration de la valeur et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_force_destroy(ProductAttributeValue $value): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($value) {

            // Suppression définitive (forceDelete)
            if ($value->trashed()) {
                force_destroy_function($value); // ← suppression définitive de la valeur et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // NORMALIZE HEX COLOR (privé)
    // ─────────────────────────────────────────────
    /**
     * Normalise et sécurise le code couleur HEX d'une valeur d'attribut.
     *
     * Règles :
     *  - le champ n'est conservé QUE si l'attribut parent est de type "color" ;
     *  - le code est trim + lowercased ;
     *  - un champ vide/absent renvoie null (couleur non renseignée).
     *
     * La validité stricte du format (règle Laravel `hex_color`) est assurée
     * en amont par les Form Requests ; ce garde-fou garantit seulement la
     * cohérence en base si la méthode est appelée hors requête HTTP.
     */
    private function normalizeHexColor(int $attributeId, ?string $hexColor): ?string
    {
        $isColor = ProductAttribute::query()
            ->whereKey($attributeId)
            ->where('type', ProductAttributeType::Color)
            ->exists();

        if (! $isColor || $hexColor === null || trim($hexColor) === '') {
            return null;
        }

        return strtolower(trim($hexColor));
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(ProductAttributeValue $value, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions qui ne sont pas présentes dans les données soumises
        ProductAttributeValueTranslation::where('attribute_value_id', $value->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimées précédemment
        ProductAttributeValueTranslation::onlyTrashed()->where('attribute_value_id', $value->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit label/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de la valeur, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $value,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.productAttributeValue'),
            $this->backendHelperLangPath,
            'label',
            'slug'
        );

        // Pour chaque traduction reçue, on met à jour la traduction existante ou on la crée
        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour la valeur et la langue
            $existingTranslation = ProductAttributeValueTranslation::withTrashed()
                ->where('attribute_value_id', $value->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée,
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->backendLangPath}.trashedTranslationConflict", [
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
                    'attribute_value_id' => $value->id,
                    'lang_id' => $translation['lang_id'],
                    'label' => $translation['label'],
                ]);

            }

        }
    }
}

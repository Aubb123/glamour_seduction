<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Attributes\ProductAttributeStatus;
use App\Enums\Products\Attributes\ProductAttributeType;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductAttributeService
{
    // Chemin de base pour les messages de traduction (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/attributes/messages/attributes-messages-service.productAttributes';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE — Création d'un attribut de produit
    // ─────────────────────────────────────────────
    public function backend_product_attributes_store(array $data): ProductAttribute
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        // (création de l'attribut + sync des traductions en une seule opération atomique)
        return DB::transaction(function () use ($data) {

            // Création de l'attribut parent (hors traductions)
            // Le slug est généré automatiquement par le trait HasSlug (source = name)
            $attribute = ProductAttribute::create([
                'sort_order' => $data['sort_order'],
                'status' => ($data['status'] ?? null) === ProductAttributeStatus::Active->value
                    ? ProductAttributeStatus::Active
                    : ProductAttributeStatus::Inactive,
                'type' => ($data['type'] ?? null) === ProductAttributeType::Color->value
                    ? ProductAttributeType::Color
                    : ProductAttributeType::Text,
            ]);

            // Synchronisation des traductions (création ou mise à jour)
            // Le slug de chaque traduction est aussi généré automatiquement par HasSlug
            $this->syncTranslations($attribute, $data['translations']);

            // Retourne l'attribut créé avec ses traductions
            return $attribute;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE — Mise à jour d'un attribut existant
    // ─────────────────────────────────────────────
    public function backend_product_attributes_update(ProductAttribute $attribute, array $data): ProductAttribute
    {
        return DB::transaction(function () use ($attribute, $data) {

            $updateData = [];

            // Mise à jour de l'ordre de tri si fourni
            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            // Mise à jour du statut (toujours présent car requis par la Request)
            $updateData['status'] = ($data['status'] ?? null) === ProductAttributeStatus::Active->value
                ? ProductAttributeStatus::Active
                : ProductAttributeStatus::Inactive;

            // Mise à jour du type (text | color) si fourni
            if (isset($data['type'])) {
                $updateData['type'] = $data['type'] === ProductAttributeType::Color->value
                    ? ProductAttributeType::Color
                    : ProductAttributeType::Text;
            }

            $attribute->update($updateData);

            // Synchronisation des traductions si fournies
            if (isset($data['translations'])) {
                $this->syncTranslations($attribute, $data['translations']);
            }

            return $attribute;
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE — Activation d'un attribut (statut = active)
    // ─────────────────────────────────────────────
    public function backend_product_attributes_activate(ProductAttribute $attribute): void
    {
        DB::transaction(function () use ($attribute) {

            // Vérification que l'attribut n'est pas déjà actif
            if ($attribute->status === ProductAttributeStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            // Activation de l'attribut sélectionné
            $attribute->update([
                'status' => ProductAttributeStatus::Active,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE — Désactivation d'un attribut (statut = inactive)
    // ─────────────────────────────────────────────
    public function backend_product_attributes_deactivate(ProductAttribute $attribute): void
    {
        DB::transaction(function () use ($attribute) {

            // Vérification que l'attribut n'est pas déjà inactif
            if ($attribute->status === ProductAttributeStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            // Désactivation de l'attribut sélectionné
            $attribute->update([
                'status' => ProductAttributeStatus::Inactive,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY — Suppression temporaire (soft delete) d'un attribut
    // ─────────────────────────────────────────────
    public function backend_product_attributes_destroy(ProductAttribute $attribute): void
    {
        DB::transaction(function () use ($attribute) {

            // Vérifie que l'attribut n'est pas déjà en corbeille
            if ($attribute->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            // Vérifie les relations dépendantes (ex: valeurs d'attributs liées)
            // Si l'attribut a des valeurs, la suppression est bloquée par BackendHelper::destroy_function
            // via le contrôleur (vérification de $dependentRelations).
            $attribute->delete();
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE — Restauration d'un attribut depuis la corbeille
    // ─────────────────────────────────────────────
    public function backend_product_attributes_restore(ProductAttribute $attribute): void
    {
        DB::transaction(function () use ($attribute) {

            // Vérifie que l'attribut est bien en corbeille
            if (! $attribute->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }

            // Restauration de l'attribut (supprime le deleted_at)
            $attribute->restore();
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY — Suppression définitive d'un attribut
    // ─────────────────────────────────────────────
    public function backend_product_attributes_force_destroy(ProductAttribute $attribute): void
    {
        DB::transaction(function () use ($attribute) {

            // Suppression définitive uniquement si l'attribut est en corbeille
            if ($attribute->trashed()) {
                // force_destroy_function supprime l'entité, ses traductions et ses fichiers associés
                force_destroy_function($attribute);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS — Synchronisation des traductions (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(ProductAttribute $attribute, array $translations): void
    {
        // Récupère les IDs des langues soumises dans la requête
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement (soft delete) les traductions qui ne sont plus présentes
        ProductAttributeTranslation::where('attribute_id', $attribute->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();

        // Suppression définitive des traductions déjà en corbeille qui ne sont plus soumises
        ProductAttributeTranslation::onlyTrashed()->where('attribute_id', $attribute->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de l'attribut, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $attribute,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.productAttribute'),
            $this->backendHelperLangPath,
            'name',
            'slug'
        );

        // Pour chaque traduction reçue, on crée ou met à jour la traduction correspondante.
        // Le slug est généré automatiquement par le trait HasSlug (source = name).
        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour cet attribut et cette langue
            $existingTranslation = ProductAttributeTranslation::withTrashed()
                ->where('attribute_id', $attribute->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée (active ou en corbeille)
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement :
                // elle doit être restaurée manuellement depuis la corbeille avant de pouvoir
                // être mise à jour (remplace l'ancienne restauration automatique).
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->backendLangPath}.trashedTranslationConflict", [
                            'lang_id' => $translation['lang_id'],
                        ])
                    );
                }

                // Met à jour la traduction existante
                // Le slug est régénéré automatiquement par HasSlug si le name change.
                $existingTranslation->update([
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);

            } else {

                // Sinon, on crée une nouvelle traduction
                // Le slug est généré automatiquement par le trait HasSlug.
                ProductAttributeTranslation::create([
                    'attribute_id' => $attribute->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }
    }
}

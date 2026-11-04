<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\Attributes\StoreProductAttributeValueRequest;
use App\Http\Requests\V1\Products\Attributes\UpdateProductAttributeValueRequest;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductAttributeValueTranslation;
use App\Services\V1\Controllers\ProductAttributeValueService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Alias pour les chemins de traduction
use function __;

class ProductAttributeValueController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/attribute-values/messages/attribute-values-messages-controller.productAttributeValues';

    // Injection de dépendance du service de gestion des tags
    public function __construct(protected ProductAttributeValueService $productAttributeValueService) {}

    // Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ///////////////////  Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_index(): View
    {
        try {

            // ✅ Récupère toutes les tags avec leurs traductions et les fichiers cloud, triées par sort_order décroissant
            $values = ProductAttributeValue::withActiveTranslation()->withCount(['audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la liste des tags
            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.index', context: ['count' => $values->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les tags récupérées
            return view('v1.backend.pages.products.attribute-values.index', compact('values'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des tags
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_create(): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Attributs actifs pour le select de l'attribut parent
            $attributes = ProductAttribute::withActiveTranslation()->where('status', 'active')->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la page de création d'une tag
            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            // Affiche la vue de création d'une tag
            return view('v1.backend.pages.products.attribute-values.create', compact('langs', 'attributes'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'une tag
            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de l'accès à la page de création
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_store(StoreProductAttributeValueRequest $request): RedirectResponse
    {
        try {

            // Création de la tag via le service, en passant les données validées du formulaire
            $value = $this->productAttributeValueService->backend_product_attribute_values_store($request->validated());

            // Log de la création de la tag avec le nom de la tag créée
            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.store', context: ['tag_uuid' => $value->uuid, 'tag_name' => $value->active_translation?->label], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $value->active_translation?->label ?? '']), is_success: true, loggable: $value);

            // Redirection vers la liste des tags avec un message de succès
            return redirect()->route('backend.product-attribute-values.show', ['productAttributeValue' => $value->uuid, 'slug' => $value->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $value->active_translation?->label ?? '']));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création de la tag
            $this->logActivity(type: 'warning', action: 'backend.product-attribute-values.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création de la tag
            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveBrandWithSlugCheck(ProductAttributeValue $productAttributeValue, string $slug, string $action, string $langKey): ProductAttributeValue
    {
        $expectedSlug = $productAttributeValue->slug ?? $productAttributeValue->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['tag_uuid' => $productAttributeValue->uuid, 'tag_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $productAttributeValue);

            abort(404);
        }

        return $productAttributeValue;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_show(ProductAttributeValue $productAttributeValue, string $slug): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Vérifie que le slug correspond
            $value = $this->resolveBrandWithSlugCheck($productAttributeValue, $slug, 'backend.product-attribute-values.show', 'show');

            // Charge les compteurs (withCount)
            $value->loadCount(['audits', 'activityLogs']);

            // Récupère toutes les traductions supprimées (soft deleted) de cette tag
            $productAttributeValueTranslations = ProductAttributeValueTranslation::onlyTrashed()->where('attribute_value_id', $value->id)->get();

            $tagName = $value->active_translation?->label ?? __("{$this->backendLangPath}.show.no_name");

            // Log de la consultation du détail d'une tag
            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.show', context: ['tag_uuid' => $value->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $tagName]), is_success: true, loggable: $value);

            // Retourne la vue de la tag avec les données nécessaires
            return view('v1.backend.pages.products.attribute-values.show', compact('value', 'langs', 'productAttributeValueTranslations'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'une tag
            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.show', context: ['tag_uuid' => $productAttributeValue->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $productAttributeValue->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la tag
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_audits(ProductAttributeValue $productAttributeValue, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $value = $this->resolveBrandWithSlugCheck($productAttributeValue, $slug, 'backend.product-attribute-values.audits', 'audits');

            // Charge les compteurs (withCount)
            $value->loadCount(['audits', 'activityLogs']);

            $tagName = $value->active_translation?->label ?? '';

            $audits = $value->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.audits', context: ['tag_uuid' => $value->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $tagName]), is_success: true, loggable: $value);

            return view('v1.backend.pages.products.attribute-values.others.audits', compact('value', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.audits', context: ['tag_uuid' => $productAttributeValue->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $productAttributeValue->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_activity_logs(ProductAttributeValue $productAttributeValue, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $value = $this->resolveBrandWithSlugCheck($productAttributeValue, $slug, 'backend.product-attribute-values.activity-logs', 'activity-logs');

            // Charge les compteurs (withCount)
            $value->loadCount(['audits', 'activityLogs']);

            $tagName = $value->active_translation?->label ?? '';

            $logs = $value->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.activity-logs', context: ['tag_uuid' => $value->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $tagName]), is_success: true, loggable: $value);

            return view('v1.backend.pages.products.attribute-values.others.activity-logs', compact('value', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.activity-logs', context: ['tag_uuid' => $productAttributeValue->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $productAttributeValue->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_edit(ProductAttributeValue $productAttributeValue): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Log de l'accès à la page de modification d'une tag
            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.edit', context: ['tag_uuid' => $productAttributeValue->uuid, 'tag_name' => $productAttributeValue->active_translation?->label], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $productAttributeValue->active_translation?->label ?? '']), is_success: true, loggable: $productAttributeValue);

            $value = $productAttributeValue;

            // Attributs actifs pour le select de l'attribut parent
            $attributes = ProductAttribute::withActiveTranslation()->where('status', 'active')->orderBy('sort_order', 'desc')->get();

            // Retourne la vue d'édition de la tag avec les données nécessaires
            return view('v1.backend.pages.products.attribute-values.edit', compact('value', 'langs', 'attributes'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de modification d'une tag
            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.edit', context: ['tag_uuid' => $productAttributeValue->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $productAttributeValue->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la tag
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_update(UpdateProductAttributeValueRequest $request, ProductAttributeValue $productAttributeValue): RedirectResponse
    {
        try {

            // Mise à jour de la tag via le service, en passant les données validées du formulaire
            $this->productAttributeValueService->backend_product_attribute_values_update($productAttributeValue, $request->validated());

            // Récupère une instance fraîche pour capturer les vrais changements
            $value = $productAttributeValue->refresh();

            $tagName = $value->active_translation?->label ?? '';

            // Log de la mise à jour de la tag avec le uuid et les changements effectués
            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.update', context: ['tag_uuid' => $value->uuid, 'tag_name' => $tagName, 'changes' => $value->getChanges()], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $tagName]), is_success: true, loggable: $value);

            // Redirection vers la page de détail de la tag avec un message de succès
            return redirect()->route('backend.product-attribute-values.show', ['productAttributeValue' => $value->uuid, 'slug' => $value->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la mise à jour de la tag
            $this->logActivity(type: 'warning', action: 'backend.product-attribute-values.update', context: ['tag_uuid' => $productAttributeValue->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $productAttributeValue->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour de la tag
            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.update', context: ['tag_uuid' => $productAttributeValue->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $productAttributeValue->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_activate(Request $request, ProductAttributeValue $productAttributeValue): RedirectResponse
    {
        try {

            // Activation de la tag via le service
            $this->productAttributeValueService->backend_product_attribute_values_activate($productAttributeValue);

            $value = $productAttributeValue;
            $tagName = $value->active_translation?->label ?? '';

            // Log de l'activation de la tag
            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.activate', context: ['tag_uuid' => $value->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $tagName]), is_success: true, loggable: $value);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-values.activate', context: ['tag_uuid' => $productAttributeValue->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $productAttributeValue->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.activate', context: ['tag_uuid' => $productAttributeValue->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_deactivate(Request $request, ProductAttributeValue $productAttributeValue): RedirectResponse
    {
        try {

            // Désactivation de la tag via le service
            $this->productAttributeValueService->backend_product_attribute_values_deactivate($productAttributeValue);

            $value = $productAttributeValue;
            $tagName = $value->active_translation?->label ?? '';

            // Log de la désactivation de la tag
            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.deactivate', context: ['tag_uuid' => $value->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $tagName]), is_success: true, loggable: $value);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-values.deactivate', context: ['tag_uuid' => $productAttributeValue->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $productAttributeValue->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.deactivate', context: ['tag_uuid' => $productAttributeValue->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_destroy(ProductAttributeValue $productAttributeValue): RedirectResponse
    {
        try {

            $value = $productAttributeValue;

            $tagName = $value->active_translation?->label ?? __("{$this->backendLangPath}.destroy.unknown");

            // Suppression de la tag via le service
            $this->productAttributeValueService->backend_product_attribute_values_destroy($productAttributeValue);

            // Log de la suppression de la tag
            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.destroy', context: ['tag_uuid' => $value->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $tagName]), is_success: true, loggable: $value);

            // Redirection vers la liste des tags avec un message de succès
            return redirect()->route('backend.product-attribute-values.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression de la tag
            $this->logActivity(type: 'warning', action: 'backend.product-attribute-values.destroy', context: ['tag_uuid' => $productAttributeValue->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $productAttributeValue->uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression de la tag
            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.destroy', context: ['tag_uuid' => $productAttributeValue->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $productAttributeValue->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_trashed(): View
    {
        try {

            $values = ProductAttributeValue::onlyTrashed()->withTrashedActiveTranslation()->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de la consultation de la corbeille des tags
            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.trashed', context: ['count' => $values->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.attribute-values.trashed', compact('values'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la tag supprimée par son UUID ou échoue
            $value = ProductAttributeValue::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->productAttributeValueService->backend_product_attribute_values_restore($value);

            $tagName = $value->active_translation?->label ?? __("{$this->backendLangPath}.restore.unknown");

            // Log de la restauration de la tag
            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.restore', context: ['tag_uuid' => $value->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $tagName]), is_success: true, loggable: $value);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $tagName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-values.restore', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-values.restore', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.restore', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attribute_values_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la tag supprimée par son UUID ou échoue
            $value = ProductAttributeValue::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $tagName = $value->active_translation?->label ?? __("{$this->backendLangPath}.force_destroy.unknown");

            // Suppression définitive via le service
            $this->productAttributeValueService->backend_product_attribute_values_force_destroy($value);

            // Log de la suppression définitive de la tag
            $this->logActivity(type: 'info', action: 'backend.product-attribute-values.force_destroy', context: ['tag_uuid' => $value->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $tagName]), is_success: true, loggable: $value);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['name' => $tagName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-values.force_destroy', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-values.force_destroy', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-values.force_destroy', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}

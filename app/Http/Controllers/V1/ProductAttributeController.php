<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\Attributes\StoreProductAttributeRequest;
use App\Http\Requests\V1\Products\Attributes\UpdateProductAttributeRequest;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeTranslation;
use App\Services\V1\Controllers\ProductAttributeService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Alias pour les chemins de traduction
use function __;

class ProductAttributeController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/attributes/messages/attributes-messages-controller.productAttributes';

    // Injection de dépendance du service de gestion des tags
    public function __construct(protected ProductAttributeService $productAttributeService) {}

    // Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ///////////////////  Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_attributes_index(): View
    {
        try {

            // ✅ Récupère toutes les tags avec leurs traductions et les fichiers cloud, triées par sort_order décroissant
            $attributes = ProductAttribute::withActiveTranslation()->withCount(['audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la liste des tags
            $this->logActivity(type: 'info', action: 'backend.product-attributes.index', context: ['count' => $attributes->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les tags récupérées
            return view('v1.backend.pages.products.attributes.index', compact('attributes'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.product-attributes.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des tags
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_attributes_create(): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Log de l'accès à la page de création d'une tag
            $this->logActivity(type: 'info', action: 'backend.product-attributes.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            // Affiche la vue de création d'une tag
            return view('v1.backend.pages.products.attributes.create', compact('langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'une tag
            $this->logActivity(type: 'error', action: 'backend.product-attributes.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de l'accès à la page de création
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_attributes_store(StoreProductAttributeRequest $request): RedirectResponse
    {
        try {

            // Création de la tag via le service, en passant les données validées du formulaire
            $attribute = $this->productAttributeService->backend_product_attributes_store($request->validated());

            // Log de la création de la tag avec le nom de la tag créée
            $this->logActivity(type: 'info', action: 'backend.product-attributes.store', context: ['tag_uuid' => $attribute->uuid, 'tag_name' => $attribute->active_translation?->name], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $attribute->active_translation?->name ?? '']), is_success: true, loggable: $attribute);

            // Redirection vers la liste des tags avec un message de succès
            return redirect()->route('backend.product-attributes.show', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $attribute->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création de la tag
            $this->logActivity(type: 'warning', action: 'backend.product-attributes.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création de la tag
            $this->logActivity(type: 'error', action: 'backend.product-attributes.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveBrandWithSlugCheck(ProductAttribute $productAttribute, string $slug, string $action, string $langKey): ProductAttribute
    {
        $expectedSlug = $productAttribute->slug ?? $productAttribute->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['tag_uuid' => $productAttribute->uuid, 'tag_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $productAttribute);

            abort(404);
        }

        return $productAttribute;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_attributes_show(ProductAttribute $productAttribute, string $slug): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Vérifie que le slug correspond
            $attribute = $this->resolveBrandWithSlugCheck($productAttribute, $slug, 'backend.product-attributes.show', 'show');

            // Charge les compteurs (withCount)
            $attribute->loadCount(['audits', 'activityLogs']);

            // Récupère toutes les traductions supprimées (soft deleted) de cette tag
            $productAttributeTranslations = ProductAttributeTranslation::onlyTrashed()->where('attribute_id', $attribute->id)->get();

            $tagName = $attribute->active_translation?->name ?? __("{$this->backendLangPath}.show.no_name");

            // Log de la consultation du détail d'une tag
            $this->logActivity(type: 'info', action: 'backend.product-attributes.show', context: ['tag_uuid' => $attribute->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $tagName]), is_success: true, loggable: $attribute);

            // Retourne la vue de la tag avec les données nécessaires
            return view('v1.backend.pages.products.attributes.show', compact('attribute', 'langs', 'productAttributeTranslations'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'une tag
            $this->logActivity(type: 'error', action: 'backend.product-attributes.show', context: ['tag_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $productAttribute->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la tag
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_attributes_audits(ProductAttribute $productAttribute, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $attribute = $this->resolveBrandWithSlugCheck($productAttribute, $slug, 'backend.product-attributes.audits', 'audits');

            // Charge les compteurs (withCount)
            $attribute->loadCount(['audits', 'activityLogs']);

            $tagName = $attribute->active_translation?->name ?? '';

            $audits = $attribute->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-attributes.audits', context: ['tag_uuid' => $attribute->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $tagName]), is_success: true, loggable: $attribute);

            return view('v1.backend.pages.products.attributes.others.audits', compact('attribute', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.audits', context: ['tag_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $productAttribute->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_attributes_activity_logs(ProductAttribute $productAttribute, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $attribute = $this->resolveBrandWithSlugCheck($productAttribute, $slug, 'backend.product-attributes.activity-logs', 'activity-logs');

            // Charge les compteurs (withCount)
            $attribute->loadCount(['audits', 'activityLogs']);

            $tagName = $attribute->active_translation?->name ?? '';

            $logs = $attribute->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-attributes.activity-logs', context: ['tag_uuid' => $attribute->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $tagName]), is_success: true, loggable: $attribute);

            return view('v1.backend.pages.products.attributes.others.activity-logs', compact('attribute', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.activity-logs', context: ['tag_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $productAttribute->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_attributes_edit(ProductAttribute $productAttribute): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Log de l'accès à la page de modification d'une tag
            $this->logActivity(type: 'info', action: 'backend.product-attributes.edit', context: ['tag_uuid' => $productAttribute->uuid, 'tag_name' => $productAttribute->active_translation?->name], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $productAttribute->active_translation?->name ?? '']), is_success: true, loggable: $productAttribute);

            $attribute = $productAttribute;

            // Retourne la vue d'édition de la tag avec les données nécessaires
            return view('v1.backend.pages.products.attributes.edit', compact('attribute', 'langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de modification d'une tag
            $this->logActivity(type: 'error', action: 'backend.product-attributes.edit', context: ['tag_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $productAttribute->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la tag
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_attributes_update(UpdateProductAttributeRequest $request, ProductAttribute $productAttribute): RedirectResponse
    {
        try {

            // Mise à jour de la tag via le service, en passant les données validées du formulaire
            $this->productAttributeService->backend_product_attributes_update($productAttribute, $request->validated());

            // Récupère une instance fraîche pour capturer les vrais changements
            $attribute = $productAttribute->refresh();

            $tagName = $attribute->active_translation?->name ?? '';

            // Log de la mise à jour de la tag avec le uuid et les changements effectués
            $this->logActivity(type: 'info', action: 'backend.product-attributes.update', context: ['tag_uuid' => $attribute->uuid, 'tag_name' => $tagName, 'changes' => $attribute->getChanges()], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $tagName]), is_success: true, loggable: $attribute);

            // Redirection vers la page de détail de la tag avec un message de succès
            return redirect()->route('backend.product-attributes.show', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la mise à jour de la tag
            $this->logActivity(type: 'warning', action: 'backend.product-attributes.update', context: ['tag_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $productAttribute->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour de la tag
            $this->logActivity(type: 'error', action: 'backend.product-attributes.update', context: ['tag_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $productAttribute->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_attributes_activate(Request $request, ProductAttribute $productAttribute): RedirectResponse
    {
        try {

            // Activation de la tag via le service
            $this->productAttributeService->backend_product_attributes_activate($productAttribute);

            $attribute = $productAttribute;
            $tagName = $attribute->active_translation?->name ?? '';

            // Log de l'activation de la tag
            $this->logActivity(type: 'info', action: 'backend.product-attributes.activate', context: ['tag_uuid' => $attribute->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $tagName]), is_success: true, loggable: $attribute);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attributes.activate', context: ['tag_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $productAttribute->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.activate', context: ['tag_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_attributes_deactivate(Request $request, ProductAttribute $productAttribute): RedirectResponse
    {
        try {

            // Désactivation de la tag via le service
            $this->productAttributeService->backend_product_attributes_deactivate($productAttribute);

            $attribute = $productAttribute;
            $tagName = $attribute->active_translation?->name ?? '';

            // Log de la désactivation de la tag
            $this->logActivity(type: 'info', action: 'backend.product-attributes.deactivate', context: ['tag_uuid' => $attribute->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $tagName]), is_success: true, loggable: $attribute);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attributes.deactivate', context: ['tag_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $productAttribute->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.deactivate', context: ['tag_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attributes_destroy(ProductAttribute $productAttribute): RedirectResponse
    {
        try {

            $attribute = $productAttribute;

            $tagName = $attribute->active_translation?->name ?? __("{$this->backendLangPath}.destroy.unknown");

            // Suppression de la tag via le service
            $this->productAttributeService->backend_product_attributes_destroy($productAttribute);

            // Log de la suppression de la tag
            $this->logActivity(type: 'info', action: 'backend.product-attributes.destroy', context: ['tag_uuid' => $attribute->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $tagName]), is_success: true, loggable: $attribute);

            // Redirection vers la liste des tags avec un message de succès
            return redirect()->route('backend.product-attributes.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression de la tag
            $this->logActivity(type: 'warning', action: 'backend.product-attributes.destroy', context: ['tag_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $productAttribute->uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression de la tag
            $this->logActivity(type: 'error', action: 'backend.product-attributes.destroy', context: ['tag_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $productAttribute->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_attributes_trashed(): View
    {
        try {

            $attributes = ProductAttribute::onlyTrashed()->withTrashedActiveTranslation()->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de la consultation de la corbeille des tags
            $this->logActivity(type: 'info', action: 'backend.product-attributes.trashed', context: ['count' => $attributes->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.attributes.trashed', compact('attributes'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_attributes_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la tag supprimée par son UUID ou échoue
            $attribute = ProductAttribute::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->productAttributeService->backend_product_attributes_restore($attribute);

            $tagName = $attribute->active_translation?->name ?? __("{$this->backendLangPath}.restore.unknown");

            // Log de la restauration de la tag
            $this->logActivity(type: 'info', action: 'backend.product-attributes.restore', context: ['tag_uuid' => $attribute->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $tagName]), is_success: true, loggable: $attribute);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $tagName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attributes.restore', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attributes.restore', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.restore', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attributes_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la tag supprimée par son UUID ou échoue
            $attribute = ProductAttribute::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $tagName = $attribute->active_translation?->name ?? __("{$this->backendLangPath}.force_destroy.unknown");

            // Suppression définitive via le service
            $this->productAttributeService->backend_product_attributes_force_destroy($attribute);

            // Log de la suppression définitive de la tag
            $this->logActivity(type: 'info', action: 'backend.product-attributes.force_destroy', context: ['tag_uuid' => $attribute->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $tagName]), is_success: true, loggable: $attribute);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['name' => $tagName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attributes.force_destroy', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attributes.force_destroy', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.force_destroy', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}

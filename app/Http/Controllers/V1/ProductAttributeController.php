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
    use LogActivity; // Trait de journalisation des activités

    // Chemin de base centralisé pour les traductions
    protected string $backendLangPath = 'v1/backend/views/pages/products/attributes/messages/attributes-messages-controller.productAttributes';

    // Injection du service de gestion des attributs de produit
    public function __construct(protected ProductAttributeService $productAttributeService) {}

    // Fonctionnalités du backend

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_attributes_index(): View
    {
        try {

            // Récupère les attributs de produit avec leur traduction active et leurs compteurs, triés par ordre décroissant
            $attributes = ProductAttribute::withActiveTranslation()->withCount(['audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            // Journalise l'accès à la liste des attributs de produit
            $this->logActivity(type: 'info', action: 'backend.product-attributes.index', context: ['count' => $attributes->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la liste des attributs de produit
            return view('v1.backend.pages.products.attributes.index', compact('attributes'));

        } catch (\Exception $e) {

            // Journalise l'erreur survenue lors de la récupération des attributs de produit
            $this->logActivity(type: 'error', action: 'backend.product-attributes.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur lors de la récupération des attributs de produit
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_attributes_create(): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives à transmettre à la vue

            // Journalise l'accès à la page de création d'un attribut de produit
            $this->logActivity(type: 'info', action: 'backend.product-attributes.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            // Affiche le formulaire de création d'un attribut de produit
            return view('v1.backend.pages.products.attributes.create', compact('langs'));

        } catch (\Exception $e) {

            // Journalise l'erreur survenue lors de l'accès au formulaire de création
            $this->logActivity(type: 'error', action: 'backend.product-attributes.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500); // Erreur serveur lors de l'accès au formulaire de création
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_attributes_store(StoreProductAttributeRequest $request): RedirectResponse
    {
        try {

            // Crée l'attribut de produit à partir des données validées du formulaire
            $attribute = $this->productAttributeService->backend_product_attributes_store($request->validated());

            // Journalise la création de l'attribut de produit
            $this->logActivity(type: 'info', action: 'backend.product-attributes.store', context: ['attribute_uuid' => $attribute->uuid, 'attribute_name' => $attribute->active_translation?->name], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $attribute->active_translation?->name ?? '']), is_success: true, loggable: $attribute);

            // Redirige vers le détail de l'attribut de produit créé
            return redirect()->route('backend.product-attributes.show', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $attribute->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            // Journalise l'erreur métier survenue lors de la création
            $this->logActivity(type: 'warning', action: 'backend.product-attributes.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Retourne au formulaire avec le message métier et les données saisies
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Journalise l'erreur serveur survenue lors de la création
            $this->logActivity(type: 'error', action: 'backend.product-attributes.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Retourne au formulaire avec un message d'erreur et les données saisies
            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution de l'attribut et vérification du slug
    // ─────────────────────────────────────────────
    private function resolveProductAttributeWithSlugCheck(ProductAttribute $productAttribute, string $slug, string $action, string $langKey): ProductAttribute
    {
        $expectedSlug = $productAttribute->slug ?? $productAttribute->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Journalise l'incohérence entre le slug reçu et le slug attendu
            $this->logActivity(type: 'warning', action: $action, context: ['attribute_uuid' => $productAttribute->uuid, 'attribute_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $productAttribute);

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

            $langs = getActiveLangs()['langs']; // Récupère les langues actives à transmettre à la vue

            // Vérifie que le slug correspond à l'attribut de produit
            $attribute = $this->resolveProductAttributeWithSlugCheck($productAttribute, $slug, 'backend.product-attributes.show', 'show');

            // Charge les compteurs associés à l'attribut de produit
            $attribute->loadCount(['audits', 'activityLogs']);

            // Récupère les traductions supprimées de l'attribut de produit
            $productAttributeTranslations = ProductAttributeTranslation::onlyTrashed()->where('attribute_id', $attribute->id)->get();

            $attributeName = $attribute->active_translation?->name ?? __("{$this->backendLangPath}.show.no_name");

            // Journalise la consultation du détail de l'attribut de produit
            $this->logActivity(type: 'info', action: 'backend.product-attributes.show', context: ['attribute_uuid' => $attribute->uuid, 'attribute_name' => $attributeName], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $attributeName]), is_success: true, loggable: $attribute);

            // Affiche le détail de l'attribut de produit et ses traductions
            return view('v1.backend.pages.products.attributes.show', compact('attribute', 'langs', 'productAttributeTranslations'));

        } catch (\Exception $e) {

            // Journalise l'erreur survenue lors de la consultation du détail
            $this->logActivity(type: 'error', action: 'backend.product-attributes.show', context: ['attribute_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $productAttribute->uuid]), is_success: false);

            abort(500); // Erreur serveur lors de la récupération du détail de l'attribut de produit
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_attributes_audits(ProductAttribute $productAttribute, string $slug): View
    {
        try {

            // Vérifie que le slug correspond à l'attribut de produit
            $attribute = $this->resolveProductAttributeWithSlugCheck($productAttribute, $slug, 'backend.product-attributes.audits', 'audits');

            // Charge les compteurs associés à l'attribut de produit
            $attribute->loadCount(['audits', 'activityLogs']);

            $attributeName = $attribute->active_translation?->name ?? '';

            $audits = $attribute->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-attributes.audits', context: ['attribute_uuid' => $attribute->uuid, 'attribute_name' => $attributeName], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $attributeName]), is_success: true, loggable: $attribute);

            return view('v1.backend.pages.products.attributes.others.audits', compact('attribute', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.audits', context: ['attribute_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $productAttribute->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_attributes_activity_logs(ProductAttribute $productAttribute, string $slug): View
    {
        try {

            // Vérifie que le slug correspond à l'attribut de produit
            $attribute = $this->resolveProductAttributeWithSlugCheck($productAttribute, $slug, 'backend.product-attributes.activity-logs', 'activity-logs');

            // Charge les compteurs associés à l'attribut de produit
            $attribute->loadCount(['audits', 'activityLogs']);

            $attributeName = $attribute->active_translation?->name ?? '';

            $logs = $attribute->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-attributes.activity-logs', context: ['attribute_uuid' => $attribute->uuid, 'attribute_name' => $attributeName], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $attributeName]), is_success: true, loggable: $attribute);

            return view('v1.backend.pages.products.attributes.others.activity-logs', compact('attribute', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.activity-logs', context: ['attribute_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $productAttribute->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_attributes_edit(ProductAttribute $productAttribute): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives à transmettre à la vue

            // Journalise l'accès à la page de modification de l'attribut de produit
            $this->logActivity(type: 'info', action: 'backend.product-attributes.edit', context: ['attribute_uuid' => $productAttribute->uuid, 'attribute_name' => $productAttribute->active_translation?->name], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $productAttribute->active_translation?->name ?? '']), is_success: true, loggable: $productAttribute);

            $attribute = $productAttribute;

            // Affiche le formulaire de modification de l'attribut de produit
            return view('v1.backend.pages.products.attributes.edit', compact('attribute', 'langs'));

        } catch (\Exception $e) {

            // Journalise l'erreur survenue lors de l'accès au formulaire de modification
            $this->logActivity(type: 'error', action: 'backend.product-attributes.edit', context: ['attribute_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $productAttribute->uuid]), is_success: false);

            abort(500); // Erreur serveur lors de la récupération de l'attribut de produit
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_attributes_update(UpdateProductAttributeRequest $request, ProductAttribute $productAttribute): RedirectResponse
    {
        try {

            // Met à jour l'attribut de produit à partir des données validées du formulaire
            $this->productAttributeService->backend_product_attributes_update($productAttribute, $request->validated());

            // Recharge le modèle afin de récupérer les modifications effectuées
            $attribute = $productAttribute->refresh();

            $attributeName = $attribute->active_translation?->name ?? '';

            // Journalise la mise à jour de l'attribut de produit et les changements effectués
            $this->logActivity(type: 'info', action: 'backend.product-attributes.update', context: ['attribute_uuid' => $attribute->uuid, 'attribute_name' => $attributeName, 'changes' => $attribute->getChanges()], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $attributeName]), is_success: true, loggable: $attribute);

            // Redirige vers le détail de l'attribut de produit mis à jour
            return redirect()->route('backend.product-attributes.show', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $attributeName]));

        } catch (\InvalidArgumentException $e) {

            // Journalise l'erreur métier survenue lors de la mise à jour
            $this->logActivity(type: 'warning', action: 'backend.product-attributes.update', context: ['attribute_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $productAttribute->uuid]), is_success: false);

            // Retourne au formulaire avec le message métier et les données saisies
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Journalise l'erreur serveur survenue lors de la mise à jour
            $this->logActivity(type: 'error', action: 'backend.product-attributes.update', context: ['attribute_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $productAttribute->uuid]), is_success: false);

            // Retourne au formulaire avec un message d'erreur et les données saisies
            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_attributes_activate(Request $request, ProductAttribute $productAttribute): RedirectResponse
    {
        try {

            // Active l'attribut de produit via le service
            $this->productAttributeService->backend_product_attributes_activate($productAttribute);

            $attribute = $productAttribute;
            $attributeName = $attribute->active_translation?->name ?? '';

            // Journalise l'activation de l'attribut de produit
            $this->logActivity(type: 'info', action: 'backend.product-attributes.activate', context: ['attribute_uuid' => $attribute->uuid, 'attribute_name' => $attributeName], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $attributeName]), is_success: true, loggable: $attribute);

            // Retourne à la page précédente avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $attributeName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attributes.activate', context: ['attribute_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $productAttribute->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.activate', context: ['attribute_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_attributes_deactivate(Request $request, ProductAttribute $productAttribute): RedirectResponse
    {
        try {

            // Désactive l'attribut de produit via le service
            $this->productAttributeService->backend_product_attributes_deactivate($productAttribute);

            $attribute = $productAttribute;
            $attributeName = $attribute->active_translation?->name ?? '';

            // Journalise la désactivation de l'attribut de produit
            $this->logActivity(type: 'info', action: 'backend.product-attributes.deactivate', context: ['attribute_uuid' => $attribute->uuid, 'attribute_name' => $attributeName], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $attributeName]), is_success: true, loggable: $attribute);

            // Retourne à la page précédente avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $attributeName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attributes.deactivate', context: ['attribute_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $productAttribute->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.deactivate', context: ['attribute_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error"), is_success: false);

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

            $attributeName = $attribute->active_translation?->name ?? __("{$this->backendLangPath}.destroy.unknown");

            // Supprime l'attribut de produit via le service
            $this->productAttributeService->backend_product_attributes_destroy($productAttribute);

            // Journalise la suppression de l'attribut de produit
            $this->logActivity(type: 'info', action: 'backend.product-attributes.destroy', context: ['attribute_uuid' => $attribute->uuid, 'attribute_name' => $attributeName], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $attributeName]), is_success: true, loggable: $attribute);

            // Redirige vers la liste des attributs de produit
            return redirect()->route('backend.product-attributes.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $attributeName]));

        } catch (\InvalidArgumentException $e) {

            // Journalise l'erreur métier survenue lors de la suppression
            $this->logActivity(type: 'warning', action: 'backend.product-attributes.destroy', context: ['attribute_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $productAttribute->uuid]), is_success: false);

            // Retourne à la page précédente avec un message d'erreur métier
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Journalise l'erreur serveur survenue lors de la suppression
            $this->logActivity(type: 'error', action: 'backend.product-attributes.destroy', context: ['attribute_uuid' => $productAttribute->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $productAttribute->uuid]), is_success: false);

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

            // Journalise la consultation de la corbeille des attributs de produit
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

            // Récupère l'attribut de produit supprimé par son UUID ou lève une exception
            $attribute = ProductAttribute::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restaure l'attribut de produit via le service
            $this->productAttributeService->backend_product_attributes_restore($attribute);

            $attributeName = $attribute->active_translation?->name ?? __("{$this->backendLangPath}.restore.unknown");

            // Journalise la restauration de l'attribut de produit
            $this->logActivity(type: 'info', action: 'backend.product-attributes.restore', context: ['attribute_uuid' => $attribute->uuid, 'attribute_name' => $attributeName], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $attributeName]), is_success: true, loggable: $attribute);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $attributeName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attributes.restore', context: ['attribute_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attributes.restore', context: ['attribute_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.restore', context: ['attribute_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attributes_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère l'attribut de produit supprimé par son UUID ou lève une exception
            $attribute = ProductAttribute::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $attributeName = $attribute->active_translation?->name ?? __("{$this->backendLangPath}.force_destroy.unknown");

            // Supprime définitivement l'attribut de produit via le service
            $this->productAttributeService->backend_product_attributes_force_destroy($attribute);

            // Journalise la suppression définitive de l'attribut de produit
            $this->logActivity(type: 'info', action: 'backend.product-attributes.force_destroy', context: ['attribute_uuid' => $attribute->uuid, 'attribute_name' => $attributeName], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $attributeName]), is_success: true, loggable: $attribute);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['name' => $attributeName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attributes.force_destroy', context: ['attribute_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attributes.force_destroy', context: ['attribute_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attributes.force_destroy', context: ['attribute_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
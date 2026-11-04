<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\Brands\StoreProductBrandRequest;
use App\Http\Requests\V1\Products\Brands\UpdateProductBrandRequest;
use App\Models\ProductBrand;
use App\Models\ProductBrandTranslation;
use App\Services\V1\Controllers\ProductBrandService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Alias pour les chemins de traduction
use function __;

class ProductBrandController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/brands/messages/brands-messages-controller.productBrands';

    // Injection de dépendance du service de gestion des marques
    public function __construct(protected ProductBrandService $productBrandService) {}

    // Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ///////////////////  Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_brands_index(): View
    {
        try {

            // ✅ Récupère toutes les marques avec leurs traductions et les fichiers cloud, triées par sort_order décroissant
            $brands = ProductBrand::with(['cloudFiles'])->withActiveTranslation()->withCount(['audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la liste des marques
            $this->logActivity(type: 'info', action: 'backend.product-brands.index', context: ['count' => $brands->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les marques récupérées
            return view('v1.backend.pages.products.brands.index', compact('brands'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.product-brands.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            // Erreur serveur générique en cas de problème lors de la récupération des marques
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_brands_create(): View
    {
        try {

            // Récupère les langues actives pour les passer à la vue
            $langs = getActiveLangs()['langs'];

            // Log de l'accès à la page de création d'une marque
            $this->logActivity(type: 'info', action: 'backend.product-brands.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            // Affiche la vue de création d'une marque
            return view('v1.backend.pages.products.brands.create', compact('langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'une marque
            $this->logActivity(type: 'error', action: 'backend.product-brands.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            // Erreur serveur générique en cas de problème lors de l'accès à la page de création
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_brands_store(StoreProductBrandRequest $request): RedirectResponse
    {
        try {

            // Création de la marque via le service, en passant les données validées du formulaire
            $brand = $this->productBrandService->backend_product_brands_store($request->validated());

            // Log de la création de la marque avec le nom de la marque créée
            $this->logActivity(type: 'info', action: 'backend.product-brands.store', context: ['brand_uuid' => $brand->uuid, 'brand_name' => $brand->active_translation?->name], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $brand->active_translation?->name ?? '']), is_success: true, loggable: $brand);

            // Redirection vers la liste des marques avec un message de succès
            return redirect()->route('backend.product-brands.show', ['productBrand' => $brand->uuid, 'slug' => $brand->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $brand->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création de la marque
            $this->logActivity(type: 'warning', action: 'backend.product-brands.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création de la marque
            $this->logActivity(type: 'error', action: 'backend.product-brands.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveBrandWithSlugCheck(ProductBrand $productBrand, string $slug, string $action, string $langKey): ProductBrand
    {
        // Vérifie que le slug correspond à la marque, sinon renvoie une 404
        $expectedSlug = $productBrand->slug ?? $productBrand->active_translation?->slug ?? 'null';

        // Si le slug fourni ne correspond pas au slug attendu, on log l'erreur et on renvoie une 404
        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['brand_uuid' => $productBrand->uuid, 'brand_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $productBrand);

            abort(404);
        }

        return $productBrand;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_brands_show(ProductBrand $productBrand, string $slug): View
    {
        try {

            // Récupère les langues actives pour les passer à la vue
            $langs = getActiveLangs()['langs'];

            // Vérifie que le slug correspond
            $brand = $this->resolveBrandWithSlugCheck($productBrand, $slug, 'backend.product-brands.show', 'show');

            // Charge les traductions non supprimées avec leur langue (pour l'affichage de toutes les langues)
            // + les compteurs (withCount)
            $brand->load(['translations' => fn ($q) => $q->whereNull('deleted_at')->with('lang')])->loadCount(['audits', 'activityLogs']);

            // Récupère toutes les traductions supprimées (soft deleted) de cette marque
            $productBrandTranslations = ProductBrandTranslation::onlyTrashed()->where('brand_id', $brand->id)->get();

            // $brandName est utilisé pour le logging et l'affichage, avec un fallback si le nom n'est pas disponible
            $brandName = $brand->active_translation?->name ?? __("{$this->backendLangPath}.show.no_name");

            // Log de la consultation du détail d'une marque
            $this->logActivity(type: 'info', action: 'backend.product-brands.show', context: ['brand_uuid' => $brand->uuid, 'brand_name' => $brandName], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $brandName]), is_success: true, loggable: $brand);

            // Retourne la vue de la marque avec les données nécessaires
            return view('v1.backend.pages.products.brands.show', compact('brand', 'langs', 'productBrandTranslations'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'une marque
            $this->logActivity(type: 'error', action: 'backend.product-brands.show', context: ['brand_uuid' => $productBrand->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $productBrand->uuid]), is_success: false);

            // Erreur serveur générique en cas de problème lors de la récupération de la marque
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_brands_audits(ProductBrand $productBrand, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $brand = $this->resolveBrandWithSlugCheck($productBrand, $slug, 'backend.product-brands.audits', 'audits');

            // Charge les compteurs (withCount)
            $brand->loadCount(['audits', 'activityLogs']);

            // $brandName est utilisé pour le logging et l'affichage, avec un fallback si le nom n'est pas disponible
            $brandName = $brand->active_translation?->name ?? '';

            // $audits récupère tous les audits de la marque, avec les relations user et auditable, triés par date de création décroissante
            $audits = $brand->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            // Log de la consultation des audits d'une marque
            $this->logActivity(type: 'info', action: 'backend.product-brands.audits', context: ['brand_uuid' => $brand->uuid, 'brand_name' => $brandName], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $brandName]), is_success: true, loggable: $brand);

            // Retourne la vue des audits de la marque avec les données nécessaires
            return view('v1.backend.pages.products.brands.others.audits', compact('brand', 'audits'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des audits d'une marque
            $this->logActivity(type: 'error', action: 'backend.product-brands.audits', context: ['brand_uuid' => $productBrand->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $productBrand->uuid]), is_success: false);

            // Erreur serveur générique en cas de problème lors de la récupération des audits
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_brands_activity_logs(ProductBrand $productBrand, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $brand = $this->resolveBrandWithSlugCheck($productBrand, $slug, 'backend.product-brands.activity-logs', 'activity-logs');

            // Charge les compteurs (withCount)
            $brand->loadCount(['audits', 'activityLogs']);

            // $brandName est utilisé pour le logging et l'affichage, avec un fallback si le nom n'est pas disponible
            $brandName = $brand->active_translation?->name ?? '';

            // $logs récupère tous les logs d'activité de la marque, avec la relation user, triés par date de création décroissante
            $logs = $brand->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            // Log de la consultation des logs d'activité d'une marque
            $this->logActivity(type: 'info', action: 'backend.product-brands.activity-logs', context: ['brand_uuid' => $brand->uuid, 'brand_name' => $brandName], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $brandName]), is_success: true, loggable: $brand);

            // Retourne la vue des logs d'activité de la marque avec les données nécessaires
            return view('v1.backend.pages.products.brands.others.activity-logs', compact('brand', 'logs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des logs d'activité d'une marque
            $this->logActivity(type: 'error', action: 'backend.product-brands.activity-logs', context: ['brand_uuid' => $productBrand->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $productBrand->uuid]), is_success: false);

            // Erreur serveur générique en cas de problème lors de la récupération des logs d'activité
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_brands_edit(ProductBrand $productBrand): View
    {
        try {

            // Récupère les langues actives pour les passer à la vue
            $langs = getActiveLangs()['langs'];

            // Log de l'accès à la page de modification d'une marque
            $this->logActivity(type: 'info', action: 'backend.product-brands.edit', context: ['brand_uuid' => $productBrand->uuid, 'brand_name' => $productBrand->active_translation?->name], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $productBrand->active_translation?->name ?? '']), is_success: true, loggable: $productBrand);

            // $brand est utilisé pour passer les données de la marque à la vue
            $brand = $productBrand;

            // Retourne la vue d'édition de la marque avec les données nécessaires
            return view('v1.backend.pages.products.brands.edit', compact('brand', 'langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de modification d'une marque
            $this->logActivity(type: 'error', action: 'backend.product-brands.edit', context: ['brand_uuid' => $productBrand->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $productBrand->uuid]), is_success: false);

            // Erreur serveur générique en cas de problème lors de la récupération de la marque
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_brands_update(UpdateProductBrandRequest $request, ProductBrand $productBrand): RedirectResponse
    {
        try {

            // Mise à jour de la marque via le service, en passant les données validées du formulaire
            $this->productBrandService->backend_product_brands_update($productBrand, $request->validated());

            // Récupère une instance fraîche pour capturer les vrais changements
            $brand = $productBrand->refresh();

            // $brandName est utilisé pour le logging et l'affichage, avec un fallback si le nom n'est pas disponible
            $brandName = $brand->active_translation?->name ?? '';

            // Log de la mise à jour de la marque avec le uuid et les changements effectués
            $this->logActivity(type: 'info', action: 'backend.product-brands.update', context: ['brand_uuid' => $brand->uuid, 'brand_name' => $brandName, 'changes' => $brand->getChanges()], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $brandName]), is_success: true, loggable: $brand);

            // Redirection vers la page de détail de la marque avec un message de succès
            return redirect()->route('backend.product-brands.show', ['productBrand' => $brand->uuid, 'slug' => $brand->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $brandName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la mise à jour de la marque
            $this->logActivity(type: 'warning', action: 'backend.product-brands.update', context: ['brand_uuid' => $productBrand->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $productBrand->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour de la marque
            $this->logActivity(type: 'error', action: 'backend.product-brands.update', context: ['brand_uuid' => $productBrand->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $productBrand->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_brands_activate(Request $request, ProductBrand $productBrand): RedirectResponse
    {
        try {

            // Activation de la marque via le service
            $this->productBrandService->backend_product_brands_activate($productBrand);

            // $brand est utilisé pour le logging et l'affichage, avec un fallback si le nom n'est pas disponible
            $brand = $productBrand;
            $brandName = $brand->active_translation?->name ?? '';

            // Log de l'activation de la marque
            $this->logActivity(type: 'info', action: 'backend.product-brands.activate', context: ['brand_uuid' => $brand->uuid, 'brand_name' => $brandName], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $brandName]), is_success: true, loggable: $brand);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $brandName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de l'activation de la marque
            $this->logActivity(type: 'warning', action: 'backend.product-brands.activate', context: ['brand_uuid' => $productBrand->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $productBrand->uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'activation de la marque
            $this->logActivity(type: 'error', action: 'backend.product-brands.activate', context: ['brand_uuid' => $productBrand->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error"), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_brands_deactivate(Request $request, ProductBrand $productBrand): RedirectResponse
    {
        try {

            // Désactivation de la marque via le service
            $this->productBrandService->backend_product_brands_deactivate($productBrand);

            // $brand est utilisé pour le logging et l'affichage, avec un fallback si le nom n'est pas disponible
            $brand = $productBrand;
            $brandName = $brand->active_translation?->name ?? '';

            // Log de la désactivation de la marque
            $this->logActivity(type: 'info', action: 'backend.product-brands.deactivate', context: ['brand_uuid' => $brand->uuid, 'brand_name' => $brandName], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $brandName]), is_success: true, loggable: $brand);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $brandName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la désactivation de la marque
            $this->logActivity(type: 'warning', action: 'backend.product-brands.deactivate', context: ['brand_uuid' => $productBrand->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $productBrand->uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la désactivation de la marque
            $this->logActivity(type: 'error', action: 'backend.product-brands.deactivate', context: ['brand_uuid' => $productBrand->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error"), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_brands_destroy(ProductBrand $productBrand): RedirectResponse
    {
        try {

            // $brand est utilisé pour le logging et l'affichage, avec un fallback si le nom n'est pas disponible
            $brand = $productBrand;

            // $brandName est utilisé pour le logging et l'affichage, avec un fallback si le nom n'est pas disponible
            $brandName = $brand->active_translation?->name ?? __("{$this->backendLangPath}.destroy.unknown");

            // Suppression de la marque via le service
            $this->productBrandService->backend_product_brands_destroy($productBrand);

            // Log de la suppression de la marque
            $this->logActivity(type: 'info', action: 'backend.product-brands.destroy', context: ['brand_uuid' => $brand->uuid, 'brand_name' => $brandName], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $brandName]), is_success: true, loggable: $brand);

            // Redirection vers la liste des marques avec un message de succès
            return redirect()->route('backend.product-brands.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $brandName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression de la marque
            $this->logActivity(type: 'warning', action: 'backend.product-brands.destroy', context: ['brand_uuid' => $productBrand->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $productBrand->uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression de la marque
            $this->logActivity(type: 'error', action: 'backend.product-brands.destroy', context: ['brand_uuid' => $productBrand->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $productBrand->uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_brands_trashed(): View
    {
        try {

            // $brands récupère toutes les marques supprimées (soft deleted) avec leurs traductions actives et les fichiers cloud, triées par date de suppression décroissante
            $brands = ProductBrand::onlyTrashed()->withTrashedActiveTranslation()->with(['cloudFiles'])->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de la consultation de la corbeille des marques
            $this->logActivity(type: 'info', action: 'backend.product-brands.trashed', context: ['count' => $brands->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            // retourne la vue de la corbeille des marques avec les données nécessaires
            return view('v1.backend.pages.products.brands.trashed', compact('brands'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation de la corbeille des marques
            $this->logActivity(type: 'error', action: 'backend.product-brands.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            // Erreur serveur générique en cas de problème lors de la récupération des marques supprimées
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_brands_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la marque supprimée par son UUID ou échoue
            $brand = ProductBrand::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->productBrandService->backend_product_brands_restore($brand);

            // $brandName est utilisé pour le logging et l'affichage, avec un fallback si le nom n'est pas disponible
            $brandName = $brand->active_translation?->name ?? __("{$this->backendLangPath}.restore.unknown");

            // Log de la restauration de la marque
            $this->logActivity(type: 'info', action: 'backend.product-brands.restore', context: ['brand_uuid' => $brand->uuid, 'brand_name' => $brandName], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $brandName]), is_success: true, loggable: $brand);

            // Redirection vers la liste des marques avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $brandName]));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la marque à restaurer
            $this->logActivity(type: 'warning', action: 'backend.product-brands.restore', context: ['brand_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            // Renvoie une erreur 404 si la marque n'est pas trouvée dans les marques supprimées
            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la restauration de la marque
            $this->logActivity(type: 'warning', action: 'backend.product-brands.restore', context: ['brand_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la restauration de la marque
            $this->logActivity(type: 'error', action: 'backend.product-brands.restore', context: ['brand_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_brands_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la marque supprimée par son UUID ou échoue
            $brand = ProductBrand::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // $brandName est utilisé pour le logging et l'affichage, avec un fallback si le nom n'est pas disponible
            $brandName = $brand->active_translation?->name ?? __("{$this->backendLangPath}.force_destroy.unknown");

            // Suppression définitive via le service
            $this->productBrandService->backend_product_brands_force_destroy($brand);

            // Log de la suppression définitive de la marque
            $this->logActivity(type: 'info', action: 'backend.product-brands.force_destroy', context: ['brand_uuid' => $brand->uuid, 'brand_name' => $brandName], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $brandName]), is_success: true, loggable: $brand);

            // Redirection vers la liste des marques avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['name' => $brandName]));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la marque à supprimer définitivement
            $this->logActivity(type: 'warning', action: 'backend.product-brands.force_destroy', context: ['brand_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            // Renvoie une erreur 404 si la marque n'est pas trouvée dans les marques supprimées
            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression définitive de la marque
            $this->logActivity(type: 'warning', action: 'backend.product-brands.force_destroy', context: ['brand_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression définitive de la marque
            $this->logActivity(type: 'error', action: 'backend.product-brands.force_destroy', context: ['brand_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}

<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\Categories\StoreProductCategoryRequest;
use App\Http\Requests\V1\Products\Categories\UpdateProductCategoryRequest;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use App\Services\V1\Controllers\ProductCategoryService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Alias pour les chemins de traduction
use function __;

class ProductCategoryController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/categories/messages/categories-messages-controller.productCategories';

    // Injection de dépendance du service de gestion des catégories de produits
    public function __construct(protected ProductCategoryService $productCategoryService) {}

    // Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ///////////////////  Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_categories_index(): View
    {
        try {

            // ✅ Récupère toutes les catégories avec leurs traductions, leur parent et les fichiers cloud, triées par sort_order décroissant
            $categories = ProductCategory::with(['parent.translations', 'cloudFiles'])->withActiveTranslation()->withCount(['children', 'audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la liste des catégories
            $this->logActivity(type: 'info', action: 'backend.product-categories.index', context: ['count' => $categories->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Affiche la vue avec les catégories récupérées
            return view('v1.backend.pages.products.categories.index', compact('categories'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.product-categories.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des catégories
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_categories_create(): View
    {
        try {

            // Récupère les langues actives pour les passer à la vue
            $langs = getActiveLangs()['langs'];

            // Catégories existantes pour le select « catégorie parente »
            $parents = ProductCategory::withActiveTranslation()->orderBy('sort_order')->get();

            // Log de l'accès à la page de création d'une catégorie
            $this->logActivity(type: 'info', action: 'backend.product-categories.create', context: [], message: null, activity: __("{$this->langPath}.create.success"), is_success: true);

            // Affiche la vue de création d'une catégorie
            return view('v1.backend.pages.products.categories.create', compact('langs', 'parents'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'une catégorie
            $this->logActivity(type: 'error', action: 'backend.product-categories.create', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.create.error"), is_success: false);

            // Erreur serveur générique en cas de problème lors de l'accès à la page de création
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_categories_store(StoreProductCategoryRequest $request): RedirectResponse
    {
        try {

            // Création de la catégorie via le service, en passant les données validées du formulaire
            $category = $this->productCategoryService->backend_product_categories_store($request->validated());

            // Log de la création de la catégorie avec le nom de la catégorie créée
            $this->logActivity(type: 'info', action: 'backend.product-categories.store', context: ['category_uuid' => $category->uuid, 'category_name' => $category->active_translation?->name], message: null, activity: __("{$this->langPath}.store.success", ['name' => $category->active_translation?->name ?? '']), is_success: true, loggable: $category);

            // Redirection vers la liste des catégories avec un message de succès
            return redirect()->route('backend.product-categories.show', ['productCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null'])->with('success', __("{$this->langPath}.store.flash_success", ['name' => $category->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création de la catégorie
            $this->logActivity(type: 'warning', action: 'backend.product-categories.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création de la catégorie
            $this->logActivity(type: 'error', action: 'backend.product-categories.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->langPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveCategoryWithSlugCheck(ProductCategory $productCategory, string $slug, string $action, string $langKey): ProductCategory
    {
        // $expectedSlug prend la valeur du slug de la catégorie, ou le slug de la traduction active si le slug
        // principal est null, sinon 'null'
        $expectedSlug = $productCategory->slug ?? $productCategory->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['category_uuid' => $productCategory->uuid, 'category_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $productCategory);

            // Si le slug ne correspond pas, on retourne une erreur 404
            abort(404);
        }

        return $productCategory;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_categories_show(ProductCategory $productCategory, string $slug): View
    {
        try {

            // Récupère les langues actives pour les passer à la vue
            $langs = getActiveLangs()['langs'];

            // Vérifie que le slug correspond
            $category = $this->resolveCategoryWithSlugCheck($productCategory, $slug, 'backend.product-categories.show', 'show');

            // Charge les traductions non supprimées avec leur langue (pour l'affichage de toutes les langues)
            // + les compteurs (withCount)
            $category->load(['translations' => fn ($q) => $q->whereNull('deleted_at')->with('lang')])->loadCount(['children', 'audits', 'activityLogs']);

            // Récupère toutes les traductions supprimées (soft deleted) de cette catégorie
            $productCategoryTranslations = ProductCategoryTranslation::onlyTrashed()->where('category_id', $category->id)->get();

            // $categoryName prend le nom de la traduction active si elle existe, sinon un message par défaut "no_name"
            $categoryName = $category->active_translation?->name ?? __("{$this->langPath}.show.no_name");

            // Log de la consultation du détail d'une catégorie
            $this->logActivity(type: 'info', action: 'backend.product-categories.show', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.show.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Retourne la vue de la catégorie avec les données nécessaires
            return view('v1.backend.pages.products.categories.show', compact('category', 'langs', 'productCategoryTranslations'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'une catégorie
            $this->logActivity(type: 'error', action: 'backend.product-categories.show', context: ['category_uuid' => $productCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $productCategory->uuid]), is_success: false);

            // Erreur serveur générique en cas de problème lors de la récupération de la catégorie
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_categories_audits(ProductCategory $productCategory, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $category = $this->resolveCategoryWithSlugCheck($productCategory, $slug, 'backend.product-categories.audits', 'audits');

            // Charge les compteurs (withCount)
            $category->loadCount(['children', 'audits', 'activityLogs']);

            // $categoryName prend le nom de la traduction active si elle existe, sinon une chaîne vide
            $categoryName = $category->active_translation?->name ?? '';

            // $audits contient les audits de la catégorie, avec les relations 'user' et 'auditable',
            //  triés par date de création décroissante
            $audits = $category->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            // Log de la consultation des audits d'une catégorie
            $this->logActivity(type: 'info', action: 'backend.product-categories.audits', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.audits.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Retourne la vue des audits de la catégorie avec les données nécessaires
            return view('v1.backend.pages.products.categories.others.audits', compact('category', 'audits'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des audits d'une catégorie
            $this->logActivity(type: 'error', action: 'backend.product-categories.audits', context: ['category_uuid' => $productCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $productCategory->uuid]), is_success: false);

            // Retourne une erreur serveur générique en cas de problème lors de la récupération des audits
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_categories_activity_logs(ProductCategory $productCategory, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $category = $this->resolveCategoryWithSlugCheck($productCategory, $slug, 'backend.product-categories.activity-logs', 'activity-logs');

            // Charge les compteurs (withCount)
            $category->loadCount(['children', 'audits', 'activityLogs']);

            // $categoryName prend le nom de la traduction active si elle existe, sinon une chaîne vide
            $categoryName = $category->active_translation?->name ?? '';

            // $logs contient les journaux d'activité de la catégorie, avec la relation 'user',
            // triés par date de création décroissante
            $logs = $category->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            // Log de la consultation des journaux d'activité d'une catégorie
            $this->logActivity(type: 'info', action: 'backend.product-categories.activity-logs', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.activity-logs.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Retourne la vue des journaux d'activité de la catégorie avec les données nécessaires
            return view('v1.backend.pages.products.categories.others.activity-logs', compact('category', 'logs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des journaux d'activité d'une catégorie
            $this->logActivity(type: 'error', action: 'backend.product-categories.activity-logs', context: ['category_uuid' => $productCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $productCategory->uuid]), is_success: false);

            // Retourne une erreur serveur générique en cas de problème lors de la récupération des journaux d'activité
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_categories_edit(ProductCategory $productCategory): View
    {
        try {

            // Récupère les langues actives pour les passer à la vue
            $langs = getActiveLangs()['langs'];

            // Log de l'accès à la page de modification d'une catégorie
            $this->logActivity(type: 'info', action: 'backend.product-categories.edit', context: ['category_uuid' => $productCategory->uuid, 'category_name' => $productCategory->active_translation?->name], message: null, activity: __("{$this->langPath}.edit.success", ['name' => $productCategory->active_translation?->name ?? '']), is_success: true, loggable: $productCategory);

            // $category est l'instance de la catégorie à modifier
            $category = $productCategory;

            // Catégories candidates comme parent : toutes sauf la catégorie courante
            // (le filtre des descendants est géré par le service lors de la soumission)
            $parents = ProductCategory::withActiveTranslation()->where('id', '!=', $category->id)->orderBy('sort_order')->get();

            // Retourne la vue d'édition de la catégorie avec les données nécessaires
            return view('v1.backend.pages.products.categories.edit', compact('category', 'langs', 'parents'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de modification d'une catégorie
            $this->logActivity(type: 'error', action: 'backend.product-categories.edit', context: ['category_uuid' => $productCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.edit.error", ['uuid' => $productCategory->uuid]), is_success: false);

            // Erreur serveur générique en cas de problème lors de la récupération de la catégorie
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_categories_update(UpdateProductCategoryRequest $request, ProductCategory $productCategory): RedirectResponse
    {
        try {

            // Mise à jour de la catégorie via le service, en passant les données validées du formulaire
            $this->productCategoryService->backend_product_categories_update($productCategory, $request->validated());

            // Récupère une instance fraîche pour capturer les vrais changements
            $category = $productCategory->refresh();

            // $categoryName prend le nom de la traduction active si elle existe, sinon une chaîne vide
            $categoryName = $category->active_translation?->name ?? '';

            // Log de la mise à jour de la catégorie avec le uuid et les changements effectués
            $this->logActivity(type: 'info', action: 'backend.product-categories.update', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName, 'changes' => $category->getChanges()], message: null, activity: __("{$this->langPath}.update.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Redirection vers la page de détail de la catégorie avec un message de succès
            return redirect()->route('backend.product-categories.show', ['productCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null'])->with('success', __("{$this->langPath}.update.flash_success", ['name' => $categoryName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la mise à jour de la catégorie
            $this->logActivity(type: 'warning', action: 'backend.product-categories.update', context: ['category_uuid' => $productCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning_meta", ['uuid' => $productCategory->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour de la catégorie
            $this->logActivity(type: 'error', action: 'backend.product-categories.update', context: ['category_uuid' => $productCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.error", ['uuid' => $productCategory->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return back()->with('error', __("{$this->langPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_categories_activate(Request $request, ProductCategory $productCategory): RedirectResponse
    {
        try {

            // Activation de la catégorie via le service
            $this->productCategoryService->backend_product_categories_activate($productCategory);

            // $category est l'instance de la catégorie activée
            $category = $productCategory;

            // $categoryName prend le nom de la traduction active si elle existe, sinon une chaîne vide
            $categoryName = $category->active_translation?->name ?? '';

            // Log de l'activation de la catégorie
            $this->logActivity(type: 'info', action: 'backend.product-categories.activate', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.activate.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.activate.flash_success", ['name' => $categoryName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de l'activation de la catégorie
            $this->logActivity(type: 'warning', action: 'backend.product-categories.activate', context: ['category_uuid' => $productCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.warning_meta", ['uuid' => $productCategory->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'activation de la catégorie
            $this->logActivity(type: 'error', action: 'backend.product-categories.activate', context: ['category_uuid' => $productCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème
            return back()->with('error', __("{$this->langPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_categories_deactivate(Request $request, ProductCategory $productCategory): RedirectResponse
    {
        try {

            // Désactivation de la catégorie via le service
            $this->productCategoryService->backend_product_categories_deactivate($productCategory);

            // $category est l'instance de la catégorie désactivée
            $category = $productCategory;

            // $categoryName prend le nom de la traduction active si elle existe, sinon une chaîne vide
            $categoryName = $category->active_translation?->name ?? '';

            // Log de la désactivation de la catégorie
            $this->logActivity(type: 'info', action: 'backend.product-categories.deactivate', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.deactivate.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.deactivate.flash_success", ['name' => $categoryName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la désactivation de la catégorie
            $this->logActivity(type: 'warning', action: 'backend.product-categories.deactivate', context: ['category_uuid' => $productCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.warning_meta", ['uuid' => $productCategory->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la désactivation de la catégorie
            $this->logActivity(type: 'error', action: 'backend.product-categories.deactivate', context: ['category_uuid' => $productCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème
            return back()->with('error', __("{$this->langPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_categories_destroy(ProductCategory $productCategory): RedirectResponse
    {
        try {

            // $category est l'instance de la catégorie à supprimer
            $category = $productCategory;

            // $categoryName prend le nom de la traduction active si elle existe, sinon un message par défaut "unknown"
            $categoryName = $category->active_translation?->name ?? __("{$this->langPath}.destroy.unknown");

            // Suppression de la catégorie via le service
            $this->productCategoryService->backend_product_categories_destroy($productCategory);

            // Log de la suppression de la catégorie
            $this->logActivity(type: 'info', action: 'backend.product-categories.destroy', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.destroy.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Redirection vers la liste des catégories avec un message de succès
            return redirect()->route('backend.product-categories.index')->with('success', __("{$this->langPath}.destroy.flash_success", ['name' => $categoryName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression de la catégorie
            $this->logActivity(type: 'warning', action: 'backend.product-categories.destroy', context: ['category_uuid' => $productCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $productCategory->uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression de la catégorie
            $this->logActivity(type: 'error', action: 'backend.product-categories.destroy', context: ['category_uuid' => $productCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $productCategory->uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_categories_trashed(): View
    {
        try {

            // $categories récupère toutes les catégories supprimées (soft deleted) avec leurs traductions actives, leurs fichiers cloud et les compteurs, triées par date de suppression décroissante
            $categories = ProductCategory::onlyTrashed()->withTrashedActiveTranslation()->with(['cloudFiles'])->withCount(['children', 'audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de la consultation de la corbeille des catégories
            $this->logActivity(type: 'info', action: 'backend.product-categories.trashed', context: ['count' => $categories->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            // Retourne la vue de la corbeille des catégories avec les données nécessaires
            return view('v1.backend.pages.products.categories.trashed', compact('categories'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation de la corbeille des catégories
            $this->logActivity(type: 'error', action: 'backend.product-categories.trashed', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            // Retourne une erreur serveur générique en cas de problème lors de la récupération des catégories supprimées
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_categories_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la catégorie supprimée par son UUID ou échoue
            $category = ProductCategory::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->productCategoryService->backend_product_categories_restore($category);

            // $categoryName prend le nom de la traduction active si elle existe, sinon un message par défaut "unknown"
            $categoryName = $category->active_translation?->name ?? __("{$this->langPath}.restore.unknown");

            // Log de la restauration de la catégorie
            $this->logActivity(type: 'info', action: 'backend.product-categories.restore', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.restore.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Redirection vers la liste des catégories avec un message de succès
            return back()->with('success', __("{$this->langPath}.restore.flash_success", ['name' => $categoryName]));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la catégorie à restaurer
            $this->logActivity(type: 'warning', action: 'backend.product-categories.restore', context: ['category_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            // Si la catégorie n'est pas trouvée, on retourne une erreur 404
            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la restauration de la catégorie
            $this->logActivity(type: 'warning', action: 'backend.product-categories.restore', context: ['category_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la restauration de la catégorie
            $this->logActivity(type: 'error', action: 'backend.product-categories.restore', context: ['category_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_categories_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la catégorie supprimée par son UUID ou échoue
            $category = ProductCategory::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // $categoryName prend le nom de la traduction active si elle existe, sinon un message par défaut "unknown"
            $categoryName = $category->active_translation?->name ?? __("{$this->langPath}.force_destroy.unknown");

            // Suppression définitive via le service
            $this->productCategoryService->backend_product_categories_force_destroy($category);

            // Log de la suppression définitive de la catégorie
            $this->logActivity(type: 'info', action: 'backend.product-categories.force_destroy', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.force_destroy.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Redirection vers la liste des catégories avec un message de succès
            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success", ['name' => $categoryName]));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la catégorie à supprimer définitivement
            $this->logActivity(type: 'warning', action: 'backend.product-categories.force_destroy', context: ['category_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            // Retourne une erreur 404 si la catégorie n'est pas trouvée
            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression définitive de la catégorie
            $this->logActivity(type: 'warning', action: 'backend.product-categories.force_destroy', context: ['category_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression définitive de la catégorie
            $this->logActivity(type: 'error', action: 'backend.product-categories.force_destroy', context: ['category_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}

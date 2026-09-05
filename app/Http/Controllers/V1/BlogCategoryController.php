<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Blogs\Categories\StoreBlogCategoryRequest;
use App\Http\Requests\V1\Blogs\Categories\UpdateBlogCategoryRequest;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Services\V1\Controllers\BlogCategoryService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Alias pour les chemins de traduction
use function __;

class BlogCategoryController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/blogs/categories/messages/categories-messages-controller.categories';

    // Injection de dépendance du service de gestion des catégories de blog
    public function __construct(protected BlogCategoryService $blogCategoryService) {}

    // Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ///////////////////  Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_blog_categories_index(): View
    {
        try {

            // ✅ Récupère toutes les catégories avec leurs traductions, triées par sort_order décroissant
            $categories = BlogCategory::with(['cloudFiles'])->withActiveTranslation()->withCount(['posts', 'audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la liste des catégories
            $this->logActivity(type: 'info', action: 'backend.blog-categories.index', context: ['count' => $categories->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Affiche la vue avec les catégories récupérées
            return view('v1.backend.pages.blogs.categories.index', compact('categories'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.blog-categories.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des catégories
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_blog_categories_create(): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Log de l'accès à la page de création d'une catégorie
            $this->logActivity(type: 'info', action: 'backend.blog-categories.create', context: [], message: null, activity: __("{$this->langPath}.create.success"), is_success: true);

            // Affiche la vue de création d'une catégorie
            return view('v1.backend.pages.blogs.categories.create', compact('langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'une catégorie
            $this->logActivity(type: 'error', action: 'backend.blog-categories.create', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.create.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de l'accès à la page de création
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_blog_categories_store(StoreBlogCategoryRequest $request): RedirectResponse
    {
        try {

            // Création de la catégorie via le service, en passant les données validées du formulaire
            $category = $this->blogCategoryService->store($request->validated());

            // Log de la création de la catégorie avec le nom de la catégorie créée
            $this->logActivity(type: 'info', action: 'backend.blog-categories.store', context: ['category_uuid' => $category->uuid, 'category_name' => $category->active_translation?->name], message: null, activity: __("{$this->langPath}.store.success", ['name' => $category->active_translation?->name ?? '']), is_success: true, loggable: $category);

            // Redirection vers la liste des catégories avec un message de succès
            return redirect()->route('backend.blog-categories.show', ['blogCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null'])->with('success', __("{$this->langPath}.store.flash_success", ['name' => $category->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création de la catégorie
            $this->logActivity(type: 'warning', action: 'backend.blog-categories.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création de la catégorie
            $this->logActivity(type: 'error', action: 'backend.blog-categories.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->langPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveCategoryWithSlugCheck(BlogCategory $blogCategory, string $slug, string $action, string $langKey): BlogCategory
    {
        $expectedSlug = $blogCategory->slug ?? $blogCategory->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['category_uuid' => $blogCategory->uuid, 'category_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $blogCategory);

            abort(404);
        }

        return $blogCategory;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_blog_categories_show(BlogCategory $blogCategory, string $slug): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Vérifie que le slug correspond
            $category = $this->resolveCategoryWithSlugCheck($blogCategory, $slug, 'backend.blog-categories.show', 'show');

            // Charge les compteurs (withCount)
            $category->loadCount(['posts', 'audits', 'activityLogs']);

            // Récupère toutes les traductions supprimées (soft deleted) de cette catégorie
            $blogCategoryTranslations = BlogCategoryTranslation::onlyTrashed()->where('category_id', $category->id)->get();

            // Récupère les articles de blog de cette catégorie
            $posts = $category->posts()->withActiveTranslation()->with(['cloudFiles'])->withCount(['audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            $categoryName = $category->active_translation?->name ?? __("{$this->langPath}.show.no_name");

            // Log de la consultation du détail d'une catégorie
            $this->logActivity(type: 'info', action: 'backend.blog-categories.show', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.show.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Retourne la vue de la catégorie avec les données nécessaires
            return view('v1.backend.pages.blogs.categories.show', compact('category', 'langs', 'blogCategoryTranslations', 'posts'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'une catégorie
            $this->logActivity(type: 'error', action: 'backend.blog-categories.show', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $blogCategory->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la catégorie
        }
    }

    // ─────────────────────────────────────────────
    // POSTS
    // ─────────────────────────────────────────────
    public function backend_blog_categories_posts(BlogCategory $blogCategory, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $category = $this->resolveCategoryWithSlugCheck($blogCategory, $slug, 'backend.blog-categories.posts', 'posts');

            // Charge les compteurs (withCount)
            $category->loadCount(['posts', 'audits', 'activityLogs']);

            // Récupère les articles de blog de cette catégorie
            $posts = $category->posts()->withActiveTranslation()->with(['cloudFiles'])->withCount(['audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            $categoryName = $category->active_translation?->name ?? __("{$this->langPath}.posts.no_name");

            // Log de la consultation des articles de la catégorie
            $this->logActivity(type: 'info', action: 'backend.blog-categories.posts', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.posts.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Retourne la vue des articles de la catégorie
            return view('v1.backend.pages.blogs.categories.others.posts', compact('category', 'posts'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des articles d'une catégorie
            $this->logActivity(type: 'error', action: 'backend.blog-categories.posts', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.posts.error", ['uuid' => $blogCategory->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_blog_categories_audits(BlogCategory $blogCategory, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $category = $this->resolveCategoryWithSlugCheck($blogCategory, $slug, 'backend.blog-categories.audits', 'audits');

            // Charge les compteurs (withCount)
            $category->loadCount(['posts', 'audits', 'activityLogs']);

            $categoryName = $category->active_translation?->name ?? '';

            $audits = $category->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.blog-categories.audits', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.audits.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            return view('v1.backend.pages.blogs.categories.others.audits', compact('category', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-categories.audits', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $blogCategory->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_blog_categories_activity_logs(BlogCategory $blogCategory, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $category = $this->resolveCategoryWithSlugCheck($blogCategory, $slug, 'backend.blog-categories.activity-logs', 'activity-logs');

            // Charge les compteurs (withCount)
            $category->loadCount(['posts', 'audits', 'activityLogs']);

            $categoryName = $category->active_translation?->name ?? '';

            $logs = $category->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.blog-categories.activity-logs', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.activity-logs.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            return view('v1.backend.pages.blogs.categories.others.activity-logs', compact('category', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-categories.activity-logs', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $blogCategory->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_blog_categories_edit(BlogCategory $blogCategory): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Log de l'accès à la page de modification d'une catégorie
            $this->logActivity(type: 'info', action: 'backend.blog-categories.edit', context: ['category_uuid' => $blogCategory->uuid, 'category_name' => $blogCategory->active_translation?->name], message: null, activity: __("{$this->langPath}.edit.success", ['name' => $blogCategory->active_translation?->name ?? '']), is_success: true, loggable: $blogCategory);

            $category = $blogCategory;

            // Retourne la vue d'édition de la catégorie avec les données nécessaires
            return view('v1.backend.pages.blogs.categories.edit', compact('category', 'langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de modification d'une catégorie
            $this->logActivity(type: 'error', action: 'backend.blog-categories.edit', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.edit.error", ['uuid' => $blogCategory->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la catégorie
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_blog_categories_update(UpdateBlogCategoryRequest $request, BlogCategory $blogCategory): RedirectResponse
    {
        try {

            // Mise à jour de la catégorie via le service, en passant les données validées du formulaire
            $this->blogCategoryService->update($blogCategory, $request->validated());

            // Récupère une instance fraîche pour capturer les vrais changements
            $category = $blogCategory->refresh();

            $categoryName = $category->active_translation?->name ?? '';

            // Log de la mise à jour de la catégorie avec le uuid et les changements effectués
            $this->logActivity(type: 'info', action: 'backend.blog-categories.update', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName, 'changes' => $category->getChanges()], message: null, activity: __("{$this->langPath}.update.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Redirection vers la page de détail de la catégorie avec un message de succès
            return redirect()->route('backend.blog-categories.show', ['blogCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null'])->with('success', __("{$this->langPath}.update.flash_success", ['name' => $categoryName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la mise à jour de la catégorie
            $this->logActivity(type: 'warning', action: 'backend.blog-categories.update', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning_meta", ['uuid' => $blogCategory->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour de la catégorie
            $this->logActivity(type: 'error', action: 'backend.blog-categories.update', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.error", ['uuid' => $blogCategory->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return back()->with('error', __("{$this->langPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_categories_activate(Request $request, BlogCategory $blogCategory): RedirectResponse
    {
        try {

            // Activation de la catégorie via le service
            $this->blogCategoryService->activate($blogCategory);

            $category = $blogCategory;
            $categoryName = $category->active_translation?->name ?? '';

            // Log de l'activation de la catégorie
            $this->logActivity(type: 'info', action: 'backend.blog-categories.activate', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.activate.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.activate.flash_success", ['name' => $categoryName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-categories.activate', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.warning_meta", ['uuid' => $blogCategory->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-categories.activate', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.error"), is_success: false);

            return back()->with('error', __("{$this->langPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_categories_deactivate(Request $request, BlogCategory $blogCategory): RedirectResponse
    {
        try {

            // Désactivation de la catégorie via le service
            $this->blogCategoryService->deactivate($blogCategory);

            $category = $blogCategory;
            $categoryName = $category->active_translation?->name ?? '';

            // Log de la désactivation de la catégorie
            $this->logActivity(type: 'info', action: 'backend.blog-categories.deactivate', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.deactivate.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.deactivate.flash_success", ['name' => $categoryName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-categories.deactivate', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.warning_meta", ['uuid' => $blogCategory->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-categories.deactivate', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.error"), is_success: false);

            return back()->with('error', __("{$this->langPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_categories_destroy(BlogCategory $blogCategory): RedirectResponse
    {
        try {

            $category = $blogCategory;

            $categoryName = $category->active_translation?->name ?? __("{$this->langPath}.destroy.unknown");

            // Suppression de la catégorie via le service
            $this->blogCategoryService->destroy($blogCategory);

            // Log de la suppression de la catégorie
            $this->logActivity(type: 'info', action: 'backend.blog-categories.destroy', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.destroy.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            // Redirection vers la liste des catégories avec un message de succès
            return redirect()->route('backend.blog-categories.index')->with('success', __("{$this->langPath}.destroy.flash_success", ['name' => $categoryName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression de la catégorie
            $this->logActivity(type: 'warning', action: 'backend.blog-categories.destroy', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $blogCategory->uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-categories.destroy', context: ['category_uuid' => $blogCategory->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $blogCategory->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_blog_categories_trashed(): View
    {
        try {

            $categories = BlogCategory::onlyTrashed()->withTrashedActiveTranslation()->with(['cloudFiles'])->withCount(['posts', 'audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de la consultation de la corbeille des catégories
            $this->logActivity(type: 'info', action: 'backend.blog-categories.trashed', context: ['count' => $categories->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.blogs.categories.trashed', compact('categories'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-categories.trashed', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_categories_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la catégorie supprimée par son UUID ou échoue
            $category = BlogCategory::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->blogCategoryService->restore($category);

            $categoryName = $category->active_translation?->name ?? '';

            // Log de la restauration de la catégorie
            $this->logActivity(type: 'info', action: 'backend.blog-categories.restore', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.restore.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            return back()->with('success', __("{$this->langPath}.restore.flash_success", ['name' => $categoryName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-categories.restore', context: ['category_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-categories.restore', context: ['category_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-categories.restore', context: ['category_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_categories_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la catégorie supprimée par son UUID ou échoue
            $category = BlogCategory::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $categoryName = $category->active_translation?->name ?? __("{$this->langPath}.force_destroy.unknown");

            // Suppression définitive via le service
            $this->blogCategoryService->forceDestroy($category);

            // Log de la suppression définitive de la catégorie
            $this->logActivity(type: 'info', action: 'backend.blog-categories.force_destroy', context: ['category_uuid' => $category->uuid, 'category_name' => $categoryName], message: null, activity: __("{$this->langPath}.force_destroy.success", ['name' => $categoryName]), is_success: true, loggable: $category);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success", ['name' => $categoryName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-categories.force_destroy', context: ['category_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-categories.force_destroy', context: ['category_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-categories.force_destroy', context: ['category_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}

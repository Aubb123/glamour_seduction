<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\Tags\StoreProductTagRequest;
use App\Http\Requests\V1\Products\Tags\UpdateProductTagRequest;
use App\Models\ProductTag;
use App\Models\ProductTagTranslation;
use App\Services\V1\Controllers\ProductTagService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Alias pour les chemins de traduction
use function __;

class ProductTagController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/tags/messages/tags-messages-controller.productTags';

    // Injection de dépendance du service de gestion des tags
    public function __construct(protected ProductTagService $productTagService) {}

    // Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ///////////////////  Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_tags_index(): View
    {
        try {

            // ✅ Récupère toutes les tags avec leurs traductions et les fichiers cloud, triées par sort_order décroissant
            $tags = ProductTag::withActiveTranslation()->withCount(['audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la liste des tags
            $this->logActivity(type: 'info', action: 'backend.product-tags.index', context: ['count' => $tags->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Affiche la vue avec les tags récupérées
            return view('v1.backend.pages.products.tags.index', compact('tags'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.product-tags.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des tags
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_tags_create(): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Log de l'accès à la page de création d'une tag
            $this->logActivity(type: 'info', action: 'backend.product-tags.create', context: [], message: null, activity: __("{$this->langPath}.create.success"), is_success: true);

            // Affiche la vue de création d'une tag
            return view('v1.backend.pages.products.tags.create', compact('langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'une tag
            $this->logActivity(type: 'error', action: 'backend.product-tags.create', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.create.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de l'accès à la page de création
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_tags_store(StoreProductTagRequest $request): RedirectResponse
    {
        try {

            // Création de la tag via le service, en passant les données validées du formulaire
            $tag = $this->productTagService->store($request->validated());

            // Log de la création de la tag avec le nom de la tag créée
            $this->logActivity(type: 'info', action: 'backend.product-tags.store', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tag->active_translation?->name], message: null, activity: __("{$this->langPath}.store.success", ['name' => $tag->active_translation?->name ?? '']), is_success: true, loggable: $tag);

            // Redirection vers la liste des tags avec un message de succès
            return redirect()->route('backend.product-tags.show', ['productTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null'])->with('success', __("{$this->langPath}.store.flash_success", ['name' => $tag->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création de la tag
            $this->logActivity(type: 'warning', action: 'backend.product-tags.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création de la tag
            $this->logActivity(type: 'error', action: 'backend.product-tags.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->langPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveProductTagWithSlugCheck(ProductTag $productTag, string $slug, string $action, string $langKey): ProductTag
    {
        $expectedSlug = $productTag->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['tag_uuid' => $productTag->uuid, 'tag_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $productTag);

            abort(404);
        }

        return $productTag;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_tags_show(ProductTag $productTag, string $slug): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Vérifie que le slug correspond
            $tag = $this->resolveProductTagWithSlugCheck($productTag, $slug, 'backend.product-tags.show', 'show');

            // Charge les traductions non supprimées avec leur langue (pour l'affichage de toutes les langues)
            // + les compteurs (withCount)
            $tag->load(['translations' => fn ($q) => $q->whereNull('deleted_at')->with('lang')])->loadCount(['audits', 'activityLogs']);

            // Récupère toutes les traductions supprimées (soft deleted) de cette tag
            $productTagTranslations = ProductTagTranslation::onlyTrashed()->where('tag_id', $tag->id)->get();

            $tagName = $tag->active_translation?->name ?? __("{$this->langPath}.show.no_name");

            // Log de la consultation du détail d'une tag
            $this->logActivity(type: 'info', action: 'backend.product-tags.show', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.show.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            // Retourne la vue de la tag avec les données nécessaires
            return view('v1.backend.pages.products.tags.show', compact('tag', 'langs', 'productTagTranslations'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'une tag
            $this->logActivity(type: 'error', action: 'backend.product-tags.show', context: ['tag_uuid' => $productTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $productTag->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la tag
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_tags_audits(ProductTag $productTag, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $tag = $this->resolveProductTagWithSlugCheck($productTag, $slug, 'backend.product-tags.audits', 'audits');

            // Charge les compteurs (withCount)
            $tag->loadCount(['audits', 'activityLogs']);

            $tagName = $tag->active_translation?->name ?? '';

            $audits = $tag->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-tags.audits', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.audits.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            return view('v1.backend.pages.products.tags.others.audits', compact('tag', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-tags.audits', context: ['tag_uuid' => $productTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $productTag->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_tags_activity_logs(ProductTag $productTag, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $tag = $this->resolveProductTagWithSlugCheck($productTag, $slug, 'backend.product-tags.activity-logs', 'activity-logs');

            // Charge les compteurs (withCount)
            $tag->loadCount(['audits', 'activityLogs']);

            $tagName = $tag->active_translation?->name ?? '';

            $logs = $tag->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-tags.activity-logs', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.activity-logs.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            return view('v1.backend.pages.products.tags.others.activity-logs', compact('tag', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-tags.activity-logs', context: ['tag_uuid' => $productTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $productTag->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_tags_edit(ProductTag $productTag): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Log de l'accès à la page de modification d'une tag
            $this->logActivity(type: 'info', action: 'backend.product-tags.edit', context: ['tag_uuid' => $productTag->uuid, 'tag_name' => $productTag->active_translation?->name], message: null, activity: __("{$this->langPath}.edit.success", ['name' => $productTag->active_translation?->name ?? '']), is_success: true, loggable: $productTag);

            $tag = $productTag;

            // Retourne la vue d'édition de la tag avec les données nécessaires
            return view('v1.backend.pages.products.tags.edit', compact('tag', 'langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de modification d'une tag
            $this->logActivity(type: 'error', action: 'backend.product-tags.edit', context: ['tag_uuid' => $productTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.edit.error", ['uuid' => $productTag->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la tag
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_tags_update(UpdateProductTagRequest $request, ProductTag $productTag): RedirectResponse
    {
        try {

            // Mise à jour de la tag via le service, en passant les données validées du formulaire
            $this->productTagService->update($productTag, $request->validated());

            // Capturer les changements avant le refresh
            $changes = $productTag->getChanges();

            // Récupère une instance fraîche pour l'affichage
            $tag = $productTag->refresh();

            $tagName = $tag->active_translation?->name ?? '';

            // Log de la mise à jour de la tag avec le uuid et les changements effectués
            $this->logActivity(type: 'info', action: 'backend.product-tags.update', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName, 'changes' => $changes], message: null, activity: __("{$this->langPath}.update.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            // Redirection vers la page de détail de la tag avec un message de succès
            return redirect()->route('backend.product-tags.show', ['productTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null'])->with('success', __("{$this->langPath}.update.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la mise à jour de la tag
            $this->logActivity(type: 'warning', action: 'backend.product-tags.update', context: ['tag_uuid' => $productTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning_meta", ['uuid' => $productTag->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour de la tag
            $this->logActivity(type: 'error', action: 'backend.product-tags.update', context: ['tag_uuid' => $productTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.error", ['uuid' => $productTag->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return back()->with('error', __("{$this->langPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_tags_activate(Request $request, ProductTag $productTag): RedirectResponse
    {
        try {

            // Activation de la tag via le service
            $this->productTagService->activate($productTag);

            $tag = $productTag;
            $tagName = $tag->active_translation?->name ?? '';

            // Log de l'activation de la tag
            $this->logActivity(type: 'info', action: 'backend.product-tags.activate', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.activate.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.activate.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-tags.activate', context: ['tag_uuid' => $productTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.warning_meta", ['uuid' => $productTag->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-tags.activate', context: ['tag_uuid' => $productTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.error"), is_success: false);

            return back()->with('error', __("{$this->langPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_tags_deactivate(Request $request, ProductTag $productTag): RedirectResponse
    {
        try {

            // Désactivation de la tag via le service
            $this->productTagService->deactivate($productTag);

            $tag = $productTag;
            $tagName = $tag->active_translation?->name ?? '';

            // Log de la désactivation de la tag
            $this->logActivity(type: 'info', action: 'backend.product-tags.deactivate', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.deactivate.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.deactivate.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-tags.deactivate', context: ['tag_uuid' => $productTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.warning_meta", ['uuid' => $productTag->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-tags.deactivate', context: ['tag_uuid' => $productTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.error"), is_success: false);

            return back()->with('error', __("{$this->langPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_tags_destroy(ProductTag $productTag): RedirectResponse
    {
        try {

            $tag = $productTag;

            $tagName = $tag->active_translation?->name ?? __("{$this->langPath}.destroy.unknown");

            // Suppression de la tag via le service
            $this->productTagService->destroy($productTag);

            // Log de la suppression de la tag
            $this->logActivity(type: 'info', action: 'backend.product-tags.destroy', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.destroy.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            // Redirection vers la liste des tags avec un message de succès
            return redirect()->route('backend.product-tags.index')->with('success', __("{$this->langPath}.destroy.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression de la tag
            $this->logActivity(type: 'warning', action: 'backend.product-tags.destroy', context: ['tag_uuid' => $productTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $productTag->uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression de la tag
            $this->logActivity(type: 'error', action: 'backend.product-tags.destroy', context: ['tag_uuid' => $productTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $productTag->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_tags_trashed(): View
    {
        try {

            $tags = ProductTag::onlyTrashed()->withTrashedActiveTranslation()->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de la consultation de la corbeille des tags
            $this->logActivity(type: 'info', action: 'backend.product-tags.trashed', context: ['count' => $tags->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.tags.trashed', compact('tags'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-tags.trashed', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_tags_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la tag supprimée par son UUID ou échoue
            $tag = ProductTag::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->productTagService->restore($tag);

            $tagName = $tag->active_translation?->name ?? __("{$this->langPath}.restore.unknown");

            // Log de la restauration de la tag
            $this->logActivity(type: 'info', action: 'backend.product-tags.restore', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.restore.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            return back()->with('success', __("{$this->langPath}.restore.flash_success", ['name' => $tagName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-tags.restore', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-tags.restore', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-tags.restore', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_tags_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la tag supprimée par son UUID ou échoue
            $tag = ProductTag::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $tagName = $tag->active_translation?->name ?? __("{$this->langPath}.force_destroy.unknown");

            // Suppression définitive via le service
            $this->productTagService->forceDestroy($tag);

            // Log de la suppression définitive de la tag
            $this->logActivity(type: 'info', action: 'backend.product-tags.force_destroy', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.force_destroy.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success", ['name' => $tagName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-tags.force_destroy', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-tags.force_destroy', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-tags.force_destroy', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}

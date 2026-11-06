<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Shops\Shops\StoreShopRequest;
use App\Http\Requests\V1\Shops\Shops\UpdateShopRequest;
use App\Models\ProductWarehouse;
use App\Models\Shop;
use App\Services\V1\Controllers\ShopService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function __;

class ShopController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/shops/shops/messages/shops-messages-controller.shops';

    // Injection de dépendance du service de gestion des boutiques
    public function __construct(protected ShopService $shopService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_shops_index(): View
    {
        try {

            // Récupère toutes les boutiques avec leur traduction active, leurs entrepôts et les compteurs d'audits/logs
            $shops = Shop::withActiveTranslation()
                ->with(['warehouses'])
                ->withCount(['audits', 'activityLogs'])
                ->orderBy('sort_order')
                ->get();

            // Log de l'accès à la liste des boutiques
            $this->logActivity(type: 'info', action: 'backend.shops.index', context: ['count' => $shops->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les boutiques récupérées
            return view('v1.backend.pages.shops.shops.index', compact('shops'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.shops.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_shops_create(): View
    {
        try {

            // Récupère les langues actives pour les formulaires de traductions
            $langs = getActiveLangs()['langs'];

            // Entrepôts actifs pour le rattachement obligatoire de la boutique
            $warehouses = ProductWarehouse::active()->orderBy('name')->get();

            // Log de l'accès à la page de création
            $this->logActivity(type: 'info', action: 'backend.shops.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            // Affiche la vue de création d'une boutique
            return view('v1.backend.pages.shops.shops.create', compact('langs', 'warehouses'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.shops.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_shops_store(StoreShopRequest $request): RedirectResponse
    {
        try {

            // Création de la boutique via le service
            $shop = $this->shopService->backend_shops_store($request->validated());

            // Nom de la boutique pour les logs et messages
            $shopName = $shop->active_translation?->name ?? '';

            // Log de la création
            $this->logActivity(type: 'info', action: 'backend.shops.store', context: ['shop_uuid' => $shop->uuid, 'shop_name' => $shopName], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $shopName]), is_success: true, loggable: $shop);

            // Redirection vers le show de la boutique
            return redirect()->route('backend.shops.show', ['shop' => $shop->uuid, 'slug' => $shop->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $shopName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier
            $this->logActivity(type: 'warning', action: 'backend.shops.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.shops.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_shops_show(Shop $shop, string $slug): View
    {
        try {

            // Charge les traductions (dont trashed pour l'onglet corbeille) et les entrepôts rattachés
            $shop->load(['translations.lang', 'warehouses'])->loadCount(['audits', 'activityLogs']);

            // Vérifie la cohérence du slug avec la traduction active (redirection cohérente sinon)
            $expectedSlug = $shop->active_translation?->slug ?? 'null';

            if ($slug !== $expectedSlug) {
                return redirect()->route('backend.shops.show', ['shop' => $shop->uuid, 'slug' => $expectedSlug]);
            }

            // Log de l'accès au show
            $this->logActivity(type: 'info', action: 'backend.shops.show', context: ['shop_uuid' => $shop->uuid], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $shop->active_translation?->name ?? '']), is_success: true, loggable: $shop);

            // Affiche la vue de détail
            return view('v1.backend.pages.shops.shops.show', compact('shop'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.shops.show', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $shop->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_shops_audits(Shop $shop, string $slug): View
    {
        try {

            // Récupère les audits de la boutique (les plus récents en premier)
            $audits = $shop->audits()->with('user')->latest()->get();

            $shop->loadCount('audits');

            // Log de l'accès aux audits
            $this->logActivity(type: 'info', action: 'backend.shops.audits', context: ['shop_uuid' => $shop->uuid], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $shop->active_translation?->name ?? '']), is_success: true, loggable: $shop);

            return view('v1.backend.pages.shops.shops.others.audits', compact('shop', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shops.audits', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $shop->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_shops_activity_logs(Shop $shop, string $slug): View
    {
        try {

            // Récupère les journaux d'activité de la boutique (les plus récents en premier)
            $logs = $shop->activityLogs()->with('user')->latest()->get();

            $shop->loadCount('activityLogs');

            // Log de l'accès aux journaux d'activité
            $this->logActivity(type: 'info', action: 'backend.shops.activity-logs', context: ['shop_uuid' => $shop->uuid], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $shop->active_translation?->name ?? '']), is_success: true, loggable: $shop);

            return view('v1.backend.pages.shops.shops.others.activity-logs', compact('shop', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shops.activity-logs', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $shop->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_shops_edit(Shop $shop): View
    {
        try {

            // Charge les traductions (avec lang) et les entrepôts rattachés
            $shop->load(['translations.lang', 'warehouses']);

            // Langues actives pour les onglets de traduction
            $langs = getActiveLangs()['langs'];

            // Tous les entrepôts actifs pour le rattachement
            $warehouses = ProductWarehouse::active()->orderBy('name')->get();

            // Log de l'accès à la page d'édition
            $this->logActivity(type: 'info', action: 'backend.shops.edit', context: ['shop_uuid' => $shop->uuid], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $shop->active_translation?->name ?? '']), is_success: true, loggable: $shop);

            return view('v1.backend.pages.shops.shops.edit', compact('shop', 'langs', 'warehouses'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shops.edit', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $shop->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_shops_update(UpdateShopRequest $request, Shop $shop): RedirectResponse
    {
        try {

            // Mise à jour via le service
            $this->shopService->backend_shops_update($shop, $request->validated());

            $shopName = $shop->fresh()->active_translation?->name ?? '';

            // Log de la mise à jour
            $this->logActivity(type: 'info', action: 'backend.shops.update', context: ['shop_uuid' => $shop->uuid], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $shopName]), is_success: true, loggable: $shop);

            return redirect()->route('backend.shops.show', ['shop' => $shop->uuid, 'slug' => $shop->fresh()->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $shopName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shops.update', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $shop->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shops.update', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $shop->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_shops_activate(Shop $shop): RedirectResponse
    {
        try {

            $this->shopService->backend_shops_activate($shop);

            $this->logActivity(type: 'info', action: 'backend.shops.activate', context: ['shop_uuid' => $shop->uuid], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $shop->active_translation?->name ?? '']), is_success: true, loggable: $shop);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $shop->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shops.activate', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $shop->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shops.activate', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error", ['uuid' => $shop->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_shops_deactivate(Shop $shop): RedirectResponse
    {
        try {

            $this->shopService->backend_shops_deactivate($shop);

            $this->logActivity(type: 'info', action: 'backend.shops.deactivate', context: ['shop_uuid' => $shop->uuid], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $shop->active_translation?->name ?? '']), is_success: true, loggable: $shop);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $shop->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shops.deactivate', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $shop->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shops.deactivate', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error", ['uuid' => $shop->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_shops_destroy(Shop $shop): RedirectResponse
    {
        try {

            $shopName = $shop->active_translation?->name ?? '';

            $this->shopService->backend_shops_destroy($shop);

            $this->logActivity(type: 'info', action: 'backend.shops.destroy', context: ['shop_uuid' => $shop->uuid, 'shop_name' => $shopName], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $shopName]), is_success: true, loggable: $shop);

            return redirect()->route('backend.shops.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $shopName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shops.destroy', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $shop->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shops.destroy', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $shop->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_shops_trashed(): View
    {
        try {

            // Récupère uniquement les boutiques supprimées (soft deleted)
            $shops = Shop::onlyTrashed()->withActiveTranslation()->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.shops.trashed', context: ['count' => $shops->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.shops.shops.trashed', compact('shops'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shops.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_shops_restore(string $uuid): RedirectResponse
    {
        try {

            $shop = Shop::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->shopService->backend_shops_restore($shop);

            $this->logActivity(type: 'info', action: 'backend.shops.restore', context: ['shop_uuid' => $shop->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $shop->active_translation?->name ?? '']), is_success: true, loggable: $shop);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $shop->active_translation?->name ?? '']));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shops.restore', context: ['shop_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shops.restore', context: ['shop_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shops.restore', context: ['shop_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_shops_force_destroy(string $uuid): RedirectResponse
    {
        try {

            $shop = Shop::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->shopService->backend_shops_force_destroy($shop);

            $this->logActivity(type: 'info', action: 'backend.shops.force_destroy', context: ['shop_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $shop->active_translation?->name ?? '']), is_success: true, loggable: $shop);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['name' => $shop->active_translation?->name ?? '']));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shops.force_destroy', context: ['shop_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shops.force_destroy', context: ['shop_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shops.force_destroy', context: ['shop_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}

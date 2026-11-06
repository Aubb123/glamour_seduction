<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Shops\Positions\StorePositionRequest;
use App\Http\Requests\V1\Shops\Positions\UpdatePositionRequest;
use App\Models\Position;
use App\Services\V1\Controllers\PositionService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class PositionController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/shops/positions/messages/positions-messages-controller.positions';

    public function __construct(protected PositionService $positionService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_positions_index(): View
    {
        try {

            $positions = Position::withActiveTranslation()
                ->withCount(['audits', 'activityLogs', 'shopUsers', 'warehouseUsers'])
                ->orderBy('sort_order')
                ->get();

            $this->logActivity(type: 'info', action: 'backend.positions.index', context: ['count' => $positions->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            return view('v1.backend.pages.shops.positions.index', compact('positions'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_positions_create(): View
    {
        try {

            $langs = getActiveLangs()['langs'];

            $this->logActivity(type: 'info', action: 'backend.positions.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            return view('v1.backend.pages.shops.positions.create', compact('langs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_positions_store(StorePositionRequest $request): RedirectResponse
    {
        try {

            $position = $this->positionService->backend_positions_store($request->validated());

            $positionName = $position->active_translation?->name ?? '';

            $this->logActivity(type: 'info', action: 'backend.positions.store', context: ['position_uuid' => $position->uuid, 'position_name' => $positionName], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $positionName]), is_success: true, loggable: $position);

            return redirect()->route('backend.positions.show', ['position' => $position->uuid, 'slug' => $position->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $positionName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.positions.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_positions_show(Position $position, string $slug): View
    {
        try {

            $position->load(['translations.lang'])->loadCount(['audits', 'activityLogs', 'shopUsers', 'warehouseUsers']);

            $expectedSlug = $position->active_translation?->slug ?? 'null';

            if ($slug !== $expectedSlug) {
                return redirect()->route('backend.positions.show', ['position' => $position->uuid, 'slug' => $expectedSlug]);
            }

            $this->logActivity(type: 'info', action: 'backend.positions.show', context: ['position_uuid' => $position->uuid], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $position->active_translation?->name ?? '']), is_success: true, loggable: $position);

            return view('v1.backend.pages.shops.positions.show', compact('position'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.show', context: ['position_uuid' => $position->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $position->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_positions_audits(Position $position, string $slug): View
    {
        try {

            $audits = $position->audits()->with('user')->latest()->get();
            $position->loadCount('audits');

            $this->logActivity(type: 'info', action: 'backend.positions.audits', context: ['position_uuid' => $position->uuid], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $position->active_translation?->name ?? '']), is_success: true, loggable: $position);

            return view('v1.backend.pages.shops.positions.others.audits', compact('position', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.audits', context: ['position_uuid' => $position->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $position->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_positions_activity_logs(Position $position, string $slug): View
    {
        try {

            $logs = $position->activityLogs()->with('user')->latest()->get();
            $position->loadCount('activityLogs');

            $this->logActivity(type: 'info', action: 'backend.positions.activity-logs', context: ['position_uuid' => $position->uuid], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $position->active_translation?->name ?? '']), is_success: true, loggable: $position);

            return view('v1.backend.pages.shops.positions.others.activity-logs', compact('position', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.activity-logs', context: ['position_uuid' => $position->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $position->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_positions_edit(Position $position): View
    {
        try {

            $position->load(['translations.lang']);

            $langs = getActiveLangs()['langs'];

            $this->logActivity(type: 'info', action: 'backend.positions.edit', context: ['position_uuid' => $position->uuid], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $position->active_translation?->name ?? '']), is_success: true, loggable: $position);

            return view('v1.backend.pages.shops.positions.edit', compact('position', 'langs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.edit', context: ['position_uuid' => $position->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $position->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_positions_update(UpdatePositionRequest $request, Position $position): RedirectResponse
    {
        try {

            $this->positionService->backend_positions_update($position, $request->validated());

            $positionName = $position->fresh()->active_translation?->name ?? '';

            $this->logActivity(type: 'info', action: 'backend.positions.update', context: ['position_uuid' => $position->uuid], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $positionName]), is_success: true, loggable: $position);

            return redirect()->route('backend.positions.show', ['position' => $position->uuid, 'slug' => $position->fresh()->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $positionName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.positions.update', context: ['position_uuid' => $position->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $position->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.update', context: ['position_uuid' => $position->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $position->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_positions_activate(Position $position): RedirectResponse
    {
        try {

            $this->positionService->backend_positions_activate($position);

            $this->logActivity(type: 'info', action: 'backend.positions.activate', context: ['position_uuid' => $position->uuid], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $position->active_translation?->name ?? '']), is_success: true, loggable: $position);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $position->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.positions.activate', context: ['position_uuid' => $position->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $position->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.activate', context: ['position_uuid' => $position->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error", ['uuid' => $position->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_positions_deactivate(Position $position): RedirectResponse
    {
        try {

            $this->positionService->backend_positions_deactivate($position);

            $this->logActivity(type: 'info', action: 'backend.positions.deactivate', context: ['position_uuid' => $position->uuid], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $position->active_translation?->name ?? '']), is_success: true, loggable: $position);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $position->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.positions.deactivate', context: ['position_uuid' => $position->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $position->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.deactivate', context: ['position_uuid' => $position->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error", ['uuid' => $position->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_positions_destroy(Position $position): RedirectResponse
    {
        try {

            $positionName = $position->active_translation?->name ?? '';

            $this->positionService->backend_positions_destroy($position);

            $this->logActivity(type: 'info', action: 'backend.positions.destroy', context: ['position_uuid' => $position->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $positionName]), is_success: true, loggable: $position);

            return redirect()->route('backend.positions.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $positionName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.positions.destroy', context: ['position_uuid' => $position->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $position->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.destroy', context: ['position_uuid' => $position->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $position->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_positions_trashed(): View
    {
        try {

            $positions = Position::onlyTrashed()->withActiveTranslation()->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.positions.trashed', context: ['count' => $positions->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.shops.positions.trashed', compact('positions'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_positions_restore(string $uuid): RedirectResponse
    {
        try {

            $position = Position::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->positionService->backend_positions_restore($position);

            $this->logActivity(type: 'info', action: 'backend.positions.restore', context: ['position_uuid' => $position->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $position->active_translation?->name ?? '']), is_success: true, loggable: $position);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $position->active_translation?->name ?? '']));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.positions.restore', context: ['position_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.positions.restore', context: ['position_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.restore', context: ['position_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_positions_force_destroy(string $uuid): RedirectResponse
    {
        try {

            $position = Position::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->positionService->backend_positions_force_destroy($position);

            $this->logActivity(type: 'info', action: 'backend.positions.force_destroy', context: ['position_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $position->active_translation?->name ?? '']), is_success: true, loggable: $position);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['name' => $position->active_translation?->name ?? '']));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.positions.force_destroy', context: ['position_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.positions.force_destroy', context: ['position_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.positions.force_destroy', context: ['position_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}

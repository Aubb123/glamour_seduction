<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\PositionTranslation;
use App\Services\V1\Controllers\PositionTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class PositionTranslationController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/shops/positions/messages/positions-messages-controller.positions.translations';

    public function __construct(protected PositionTranslationService $positionTranslationService) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_position_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            $translation = PositionTranslation::where('uuid', $uuid)->firstOrFail();

            $this->positionTranslationService->backend_position_translations_destroy($translation);

            $this->logActivity(type: 'info', action: 'backend.position-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.position-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.position-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.position-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_position_translations_trashed(string $position_uuid): View
    {
        try {

            $position = Position::withTrashed()->where('uuid', $position_uuid)->firstOrFail();

            $trashedTranslations = PositionTranslation::onlyTrashed()->where('position_id', $position->id)->get();

            $this->logActivity(type: 'info', action: 'backend.position-translations.trashed', context: ['count' => $trashedTranslations->count(), 'position_uuid' => $position_uuid], message: null, activity: __("{$this->backendLangPath}.trashed.accessed", ['name' => $position->active_translation?->name ?? $position_uuid]), is_success: true, loggable: $position);

            return view('v1.backend.pages.shops.positions.translations.trashed', compact('trashedTranslations', 'position'));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.position-translations.trashed', context: ['position_uuid' => $position_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.not_found", ['uuid' => $position_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.trashed.not_found", ['uuid' => $position_uuid]));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.position-translations.trashed', context: ['position_uuid' => $position_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error", ['uuid' => $position_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.trashed.error", ['uuid' => $position_uuid]));
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_position_translations_restore(string $uuid): RedirectResponse
    {
        try {

            $translation = PositionTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->positionTranslationService->backend_position_translations_restore($translation);

            $this->logActivity(type: 'info', action: 'backend.position-translations.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.position-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.position-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.position-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_position_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            $translation = PositionTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->positionTranslationService->backend_position_translations_force_destroy($translation);

            $this->logActivity(type: 'info', action: 'backend.position-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.position-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.position-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.position-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}

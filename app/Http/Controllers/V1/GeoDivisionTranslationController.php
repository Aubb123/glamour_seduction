<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\GeoDivision;
use App\Models\GeoDivisionTranslation;
use App\Services\V1\Controllers\GeoDivisionTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class GeoDivisionTranslationController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/divisions/messages/divisions-messages-controller.divisions.translations';

    public function __construct(
        protected GeoDivisionTranslationService $geoDivisionTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_geodivisions_translations_destroy(GeoDivisionTranslation $geoDivisionTranslation): RedirectResponse
    {
        try {

            // Suppression via le service (soft delete)
            $this->geoDivisionTranslationService->backend_geodivisions_translations_destroy($geoDivisionTranslation);

            // Log de la suppression
            $this->logActivity(type: 'warning', action: 'backend.geodivisions.translation.destroy', context: ['division_translation_uuid' => $geoDivisionTranslation->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['uuid' => $geoDivisionTranslation->uuid]), is_success: true, loggable: $geoDivisionTranslation);

            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.translation.destroy', context: ['division_translation_uuid' => $geoDivisionTranslation->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $geoDivisionTranslation->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.translation.destroy', context: ['division_translation_uuid' => $geoDivisionTranslation->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $geoDivisionTranslation->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_geodivisions_translations_trashed(GeoDivision $geoDivision): View
    {
        try {

            // Division résolue par le binding implicite sur uuid (route {geoDivision} avec ->withTrashed())
            $division = $geoDivision;

            // Récupère les traductions supprimées (soft-deleted) associées à la division
            $geoDivisionTranslations = GeoDivisionTranslation::onlyTrashed()->where('division_id', $division->id)->get();

            // Log de l'accès à la corbeille des traductions
            $this->logActivity(type: 'info', action: 'backend.geodivisions.translation.trashed', context: ['division_uuid' => $division->uuid, 'division_name' => $division->active_translation?->name], message: null, activity: __("{$this->backendLangPath}.trashed.accessed", ['name' => $division->active_translation?->name]), is_success: true, loggable: $division);

            return view('v1.backend.pages.geos.divisions.translations.trashed', compact('division', 'geoDivisionTranslations'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.translation.trashed', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error", ['uuid' => $geoDivision->uuid]), is_success: false);

            return abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_geodivisions_translations_restore(string $translation_uuid): RedirectResponse
    {
        try {

            // Récupère la traduction même si elle est soft-deleted
            $geoDivisionTranslation = GeoDivisionTranslation::withTrashed()->where('uuid', $translation_uuid)->firstOrFail();

            // Restauration de la traduction via le service
            $this->geoDivisionTranslationService->backend_geodivisions_translations_restore($geoDivisionTranslation);

            // Log de la restauration
            $this->logActivity(type: 'info', action: 'backend.geodivisions.translation.restore', context: ['division_translation_uuid' => $geoDivisionTranslation->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['uuid' => $geoDivisionTranslation->uuid]), is_success: true, loggable: $geoDivisionTranslation);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.translation.restore', context: ['division_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.translation.restore', context: ['division_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.translation.restore', context: ['division_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_geodivisions_translations_force_destroy(string $translation_uuid): RedirectResponse
    {
        try {

            // Récupère la traduction uniquement si elle est en corbeille
            $geoDivisionTranslation = GeoDivisionTranslation::onlyTrashed()->where('uuid', $translation_uuid)->firstOrFail();

            // Suppression définitive via le service
            $this->geoDivisionTranslationService->backend_geodivisions_translations_force_destroy($geoDivisionTranslation);

            // Log de la suppression définitive
            $this->logActivity(type: 'warning', action: 'backend.geodivisions.translation.force_destroy', context: ['division_translation_uuid' => $geoDivisionTranslation->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['uuid' => $geoDivisionTranslation->uuid]), is_success: true, loggable: $geoDivisionTranslation);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.translation.force_destroy', context: ['division_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.translation.force_destroy', context: ['division_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.translation.force_destroy', context: ['division_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));

        }
    }
}
<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\GeoDivisionType;
use App\Models\GeoDivisionTypeTranslation;
use App\Services\V1\Controllers\GeoDivisionTypeTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GeoDivisionTypeTranslationController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/division-types/messages/division-types-messages-controller.divisionTypes.translations';

    public function __construct(
        protected GeoDivisionTypeTranslationService $geoDivisionTypeTranslationService
    ) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_translations_destroy(GeoDivisionTypeTranslation $geoDivisionTypeTranslation): RedirectResponse
    {
        try {

            // Suppression via le service (soft delete)
            $this->geoDivisionTypeTranslationService->backend_geodivisiontypes_translations_destroy($geoDivisionTypeTranslation);

            // Log de la suppression
            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.translation.destroy', context: ['division_type_translation_uuid' => $geoDivisionTypeTranslation->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['uuid' => $geoDivisionTypeTranslation->uuid]), is_success: true, loggable: $geoDivisionTypeTranslation);

            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de validation
            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.translation.destroy', context: ['division_type_translation_uuid' => $geoDivisionTypeTranslation->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $geoDivisionTypeTranslation->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.translation.destroy', context: ['division_type_translation_uuid' => $geoDivisionTypeTranslation->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $geoDivisionTypeTranslation->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_translations_trashed(GeoDivisionType $geoDivisionType): View
    {
        try {

            // Type résolu par le binding implicite sur uuid (route {geoDivisionType} avec ->withTrashed())
            $divisionType = $geoDivisionType;

            // Récupère les traductions supprimées (soft-deleted) associées au type de division
            $geoDivisionTypeTranslations = GeoDivisionTypeTranslation::onlyTrashed()->where('division_type_id', $divisionType->id)->get();

            // Log de l'accès à la corbeille des traductions d'un type de division
            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.translation.trashed', context: ['division_type_uuid' => $divisionType->uuid, 'division_type_name' => $divisionType->active_translation?->name], message: null, activity: __("{$this->backendLangPath}.trashed.accessed", ['name' => $divisionType->active_translation?->name]), is_success: true, loggable: $divisionType);

            return view('v1.backend.pages.geos.division-types.translations.trashed', compact('divisionType', 'geoDivisionTypeTranslations'));

        } catch (\Exception $e) {
            // Log de l'erreur lors de l'accès à la corbeille des traductions d'un type de division
            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.translation.trashed', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error", ['name' => $geoDivisionType->uuid]), is_success: false);

            return abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_translations_restore(string $translation_uuid): RedirectResponse
    {
        try {

            // Récupère la traduction même si elle est soft-deleted
            $geoDivisionTypeTranslation = GeoDivisionTypeTranslation::withTrashed()->where('uuid', $translation_uuid)->firstOrFail();

            // Restauration de la traduction via le service (restore)
            $this->geoDivisionTypeTranslationService->backend_geodivisiontypes_translations_restore($geoDivisionTypeTranslation);

            // Log de la restauration de la traduction
            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.translation.restore', context: ['division_type_translation_uuid' => $geoDivisionTypeTranslation->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['uuid' => $geoDivisionTypeTranslation->uuid]), is_success: true, loggable: $geoDivisionTypeTranslation);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur 404 si la traduction n'est pas trouvée
            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.translation.restore', context: ['division_type_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la restauration (conflit de nom en corbeille ou avec un autre type)
            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.translation.restore', context: ['division_type_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la restauration de la traduction
            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.translation.restore', context: ['division_type_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_translations_force_destroy(string $translation_uuid): RedirectResponse
    {
        try {

            // Récupère la traduction même si elle est soft-deleted
            $geoDivisionTypeTranslation = GeoDivisionTypeTranslation::onlyTrashed()->where('uuid', $translation_uuid)->firstOrFail();

            // Suppression définitive de la traduction via le service (force delete)
            $this->geoDivisionTypeTranslationService->backend_geodivisiontypes_translations_force_destroy($geoDivisionTypeTranslation);

            // Log de la suppression définitive de la traduction
            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.translation.force_destroy', context: ['division_type_translation_uuid' => $geoDivisionTypeTranslation->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['uuid' => $geoDivisionTypeTranslation->uuid]), is_success: true, loggable: $geoDivisionTypeTranslation);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur 404 si la traduction n'est pas trouvée
            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.translation.force_destroy', context: ['division_type_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de validation
            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.translation.force_destroy', context: ['division_type_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression définitive de la traduction
            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.translation.force_destroy', context: ['division_type_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));

        }
    }
}

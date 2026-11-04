<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\PlatformVersion;
use App\Models\PlatformVersionTranslation;
use App\Services\V1\Controllers\PlatformVersionTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlatformVersionTranslationController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/platform-versions/messages/platform-messages-controller.versions.translations';

    public function __construct(
        protected PlatformVersionTranslationService $platformVersionTranslationService
    ) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_platform_version_translations_destroy(PlatformVersionTranslation $platformVersionTranslation): RedirectResponse
    {
        try {

            // Suppression via le service (soft delete)
            $this->platformVersionTranslationService->backend_platform_version_translations_destroy($platformVersionTranslation);

            // Log de la suppression
            $this->logActivity(type: 'warning', action: 'backend.platform-version-translations.destroy', context: ['version_translation_uuid' => $platformVersionTranslation->uuid], message: null, activity: __("{$this->langPath}.destroy.success", ['uuid' => $platformVersionTranslation->uuid]), is_success: true, loggable: $platformVersionTranslation);

            return back()->with('success', __("{$this->langPath}.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de validation
            $this->logActivity(type: 'warning', action: 'backend.platform-version-translations.destroy', context: ['version_translation_uuid' => $platformVersionTranslation->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $platformVersionTranslation->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.platform-version-translations.destroy', context: ['version_translation_uuid' => $platformVersionTranslation->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $platformVersionTranslation->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_platform_version_translations_trashed(PlatformVersion $platformVersion): View
    {
        try {

            // Version résolue par le binding implicite sur uuid (route {platformVersion} avec ->withTrashed())
            $version = $platformVersion;

            // Récupère toutes les traductions supprimées (soft deleted) de cette version
            $platformVersionTranslations = PlatformVersionTranslation::onlyTrashed()->where('platform_version_id', $version->id)->get();

            // Log de l'accès à la corbeille des traductions
            $this->logActivity(type: 'info', action: 'backend.platform-version-translations.trashed', context: ['count' => $platformVersionTranslations->count(), 'version_uuid' => $version->uuid], message: null, activity: __("{$this->langPath}.trashed.success", ['code' => $version->code]), is_success: true, loggable: $version);

            // Affiche la vue avec les traductions supprimées
            return view('v1.backend.pages.platform-versions.translations.trashed', compact('platformVersionTranslations', 'version'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.platform-version-translations.trashed', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            return abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_platform_version_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée par son UUID ou échoue
            $platformVersionTranslation = PlatformVersionTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->platformVersionTranslationService->backend_platform_version_translations_restore($platformVersionTranslation);

            // Log de la restauration
            $this->logActivity(type: 'info', action: 'backend.platform-version-translations.restore', context: ['version_translation_uuid' => $platformVersionTranslation->uuid], message: null, activity: __("{$this->langPath}.restore.success", ['uuid' => $platformVersionTranslation->uuid]), is_success: true, loggable: $platformVersionTranslation);

            return back()->with('success', __("{$this->langPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur 404 si la traduction n'est pas trouvée
            $this->logActivity(type: 'warning', action: 'backend.platform-version-translations.restore', context: ['version_translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_warning"));

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.platform-version-translations.restore', context: ['version_translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_platform_version_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée par son UUID ou échoue
            $platformVersionTranslation = PlatformVersionTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service
            $this->platformVersionTranslationService->backend_platform_version_translations_force_destroy($platformVersionTranslation);

            // Log de la suppression définitive
            $this->logActivity(type: 'info', action: 'backend.platform-version-translations.force_destroy', context: ['version_translation_uuid' => $uuid], message: null, activity: __("{$this->langPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $platformVersionTranslation);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur 404 si la traduction n'est pas trouvée
            $this->logActivity(type: 'warning', action: 'backend.platform-version-translations.force_destroy', context: ['version_translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de validation
            $this->logActivity(type: 'warning', action: 'backend.platform-version-translations.force_destroy', context: ['version_translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.platform-version-translations.force_destroy', context: ['version_translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));

        }
    }
}

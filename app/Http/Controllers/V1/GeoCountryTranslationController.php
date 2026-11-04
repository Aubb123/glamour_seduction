<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\GeoCountry;
use App\Models\GeoCountryTranslation;
use App\Services\V1\Controllers\GeoCountryTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GeoCountryTranslationController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/countries/messages/countries-messages-controller.countries.translations';

    public function __construct(
        protected GeoCountryTranslationService $geoCountryTranslationService
    ) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_geocountries_translations_destroy(GeoCountryTranslation $geoCountryTranslation): RedirectResponse
    {
        try {

            // Suppression via le service (soft delete)
            $this->geoCountryTranslationService->backend_geocountries_translations_destroy($geoCountryTranslation);

            // Log de la suppression
            $this->logActivity(type: 'warning', action: 'backend.geocountries.translation.destroy', context: ['country_translation_uuid' => $geoCountryTranslation->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['uuid' => $geoCountryTranslation->uuid]), is_success: true, loggable: $geoCountryTranslation);

            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de validation
            $this->logActivity(type: 'warning', action: 'backend.geocountries.translation.destroy', context: ['country_translation_uuid' => $geoCountryTranslation->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $geoCountryTranslation->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.geocountries.translation.destroy', context: ['country_translation_uuid' => $geoCountryTranslation->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $geoCountryTranslation->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_geocountries_translations_trashed(GeoCountry $geoCountry): View
    {
        try {

            // Pays résolu par le binding implicite sur uuid (route {geoCountry} avec ->withTrashed())
            $country = $geoCountry;

            // Récupère les traductions supprimées (soft-deleted) associées au pays
            $geoCountryTranslations = GeoCountryTranslation::onlyTrashed()->where('country_id', $country->id)->get();

            // Log de l'accès à la corbeille des traductions d'un pays
            $this->logActivity(type: 'info', action: 'backend.geocountries.translation.trashed', context: ['country_uuid' => $country->uuid, 'country_name' => $country->active_translation?->name], message: null, activity: __("{$this->backendLangPath}.trashed.accessed", ['name' => $country->active_translation?->name]), is_success: true, loggable: $country);

            return view('v1.backend.pages.geos.countries.translations.trashed', compact('country', 'geoCountryTranslations'));

        } catch (\Exception $e) {
            // Log de l'erreur lors de l'accès à la corbeille des traductions d'un pays
            $this->logActivity(type: 'error', action: 'backend.geocountries.translation.trashed', context: ['country_uuid' => $geoCountry->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error", ['name' => $geoCountry->uuid]), is_success: false);

            return abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_geocountries_translations_restore(string $translation_uuid): RedirectResponse
    {
        try {

            // Récupère la traduction même si elle est soft-deleted
            $geoCountryTranslation = GeoCountryTranslation::withTrashed()->where('uuid', $translation_uuid)->firstOrFail();

            // Restauration de la traduction via le service (restore)
            $this->geoCountryTranslationService->backend_geocountries_translations_restore($geoCountryTranslation);

            // Log de la restauration de la traduction
            $this->logActivity(type: 'info', action: 'backend.geocountries.translation.restore', context: ['country_translation_uuid' => $geoCountryTranslation->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['uuid' => $geoCountryTranslation->uuid]), is_success: true, loggable: $geoCountryTranslation);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur 404 si la traduction n'est pas trouvée
            $this->logActivity(type: 'warning', action: 'backend.geocountries.translation.restore', context: ['country_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la restauration (conflit nom/slug en corbeille ou avec un autre pays)
            $this->logActivity(type: 'warning', action: 'backend.geocountries.translation.restore', context: ['country_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la restauration de la traduction
            $this->logActivity(type: 'error', action: 'backend.geocountries.translation.restore', context: ['country_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_geocountries_translations_force_destroy(string $translation_uuid): RedirectResponse
    {
        try {

            // Récupère la traduction même si elle est soft-deleted
            $geoCountryTranslation = GeoCountryTranslation::onlyTrashed()->where('uuid', $translation_uuid)->firstOrFail();

            // Suppression définitive de la traduction via le service (force delete)
            $this->geoCountryTranslationService->backend_geocountries_translations_force_destroy($geoCountryTranslation);

            // Log de la suppression définitive de la traduction
            $this->logActivity(type: 'warning', action: 'backend.geocountries.translation.force_destroy', context: ['country_translation_uuid' => $geoCountryTranslation->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['uuid' => $geoCountryTranslation->uuid]), is_success: true, loggable: $geoCountryTranslation);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur 404 si la traduction n'est pas trouvée
            $this->logActivity(type: 'warning', action: 'backend.geocountries.translation.force_destroy', context: ['country_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de validation
            $this->logActivity(type: 'warning', action: 'backend.geocountries.translation.force_destroy', context: ['country_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression définitive de la traduction
            $this->logActivity(type: 'error', action: 'backend.geocountries.translation.force_destroy', context: ['country_translation_uuid' => $translation_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $translation_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));

        }
    }
}

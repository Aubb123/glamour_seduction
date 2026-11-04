<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\GeoContinent;
use App\Models\GeoContinentTranslation;
use App\Services\V1\Controllers\GeoContinentTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GeoContinentTranslationController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/geos/continents/messages/continent-messages-controller.geocontinents.translations';

    public function __construct(
        protected GeoContinentTranslationService $geoContinentTranslationService
    ) {}

    // Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ///////////////////  Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_geocontinents_translations_destroy(GeoContinentTranslation $geoContinentTranslation): RedirectResponse
    {
        try {

            // Suppression via le service (soft delete)
            $this->geoContinentTranslationService->backend_geocontinents_translations_destroy($geoContinentTranslation);

            // Log de la suppression
            $this->logActivity(type: 'warning', action: 'backend.geo.continent.translation.destroy', context: ['continent_translation_uuid' => $geoContinentTranslation->uuid], message: null, activity: __("{$this->langPath}.destroy.success", ['uuid' => $geoContinentTranslation->uuid]), is_success: true, loggable: $geoContinentTranslation);

            return back()->with('success', __("{$this->langPath}.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de validation
            $this->logActivity(type: 'warning', action: 'backend.geo.continent.translation.destroy', context: ['continent_translation_uuid' => $geoContinentTranslation->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $geoContinentTranslation->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.geo.continent.translation.destroy', context: ['continent_translation_uuid' => $geoContinentTranslation->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $geoContinentTranslation->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_geocontinents_translations_trashed(GeoContinent $geoContinent): View
    {
        try {

            // Continent résolu par le binding implicite sur uuid (route {geoContinent} avec ->withTrashed())
            $continent = $geoContinent;

            // Récupère toutes les traductions supprimées (soft deleted) de ce continent
            $trashedTranslations = GeoContinentTranslation::onlyTrashed()->where('continent_id', $continent->id)->get();

            // Log de l'accès à la corbeille des traductions
            $this->logActivity(type: 'info', action: 'backend.geocontinents.translation.trashed', context: ['count' => $trashedTranslations->count(), 'continent_uuid' => $continent->uuid], message: null, activity: __("{$this->langPath}.trashed.success", ['slug' => $continent->slug]), is_success: true, loggable: $continent);

            // Affiche la vue avec les traductions supprimées
            return view('v1.backend.pages.geos.continents.translations.trashed', compact('trashedTranslations', 'continent'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.geocontinents.translation.trashed', context: ['continent_uuid' => $geoContinent->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            return abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_geocontinents_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée par son UUID ou échoue
            $translation = GeoContinentTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->geoContinentTranslationService->backend_geocontinents_translations_restore($translation);

            // Log de la restauration
            $this->logActivity(type: 'info', action: 'backend.geocontinents.translation.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->langPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur 404 si la traduction n'est pas trouvée
            $this->logActivity(type: 'warning', action: 'backend.geocontinents.translation.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de validation
            $this->logActivity(type: 'warning', action: 'backend.geocontinents.translation.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.geocontinents.translation.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_geocontinents_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée par son UUID ou échoue
            $translation = GeoContinentTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service
            $this->geoContinentTranslationService->backend_geocontinents_translations_force_destroy($translation);

            // Log de la suppression définitive
            $this->logActivity(type: 'info', action: 'backend.geocontinents.translation.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->langPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur 404 si la traduction n'est pas trouvée
            $this->logActivity(type: 'warning', action: 'backend.geocontinents.translation.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de validation
            $this->logActivity(type: 'warning', action: 'backend.geocontinents.translation.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.geocontinents.translation.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));

        }
    }
}

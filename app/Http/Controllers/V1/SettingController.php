<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setting\UpdateSettingRequest;
use App\Models\ActivityLog;
use App\Models\Audit;
use App\Models\Setting;
use App\Services\V1\Controllers\SettingService;
use App\Traits\LogActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class SettingController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/settings/messages/settings-messages-controller.settings';

    // Injection de dépendance du service de gestion des settings
    public function __construct(protected SettingService $settingService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX (affiche tous les settings groupés)
    // ─────────────────────────────────────────────
    public function backend_settings_index(): View
    {
        try {

            // Récupère les langues actives
            $langs = getActiveLangs()['langs'];

            // Récupère tous les settings groupés (toutes les traductions pour les onglets,
            // active_translation gère la langue courante dans la vue)
            $settings = Setting::with(['translations.lang'])->orderBy('group', 'asc')->orderBy('created_at', 'desc')->get();

            // Organise les settings par groupe
            $groupedSettings = $settings->groupBy('group');

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.settings.index', context: ['count' => $settings->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Retourne la vue avec les settings groupés
            return view('v1.backend.pages.settings.index', compact('groupedSettings', 'langs'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.settings.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_settings_audits(): View
    {
        try {

            $audits = Audit::where('auditable_type', Setting::class)->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.settings.audits', context: ['count' => $audits->count()], message: null, activity: __("{$this->langPath}.audits.success"), is_success: true);

            return view('v1.backend.pages.settings.others.audits', compact('audits'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.settings.audits', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_settings_activity_logs(): View
    {
        try {

            $logs = ActivityLog::where('loggable_type', Setting::class)->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.settings.activity-logs', context: ['count' => $logs->count()], message: null, activity: __("{$this->langPath}.activity-logs.success"), is_success: true);

            return view('v1.backend.pages.settings.others.activity-logs', compact('logs'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.settings.activity-logs', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE (sauvegarde les modifications)
    // ─────────────────────────────────────────────
    public function backend_settings_update(UpdateSettingRequest $request): RedirectResponse
    {
        try {

            $settings = $request->validated()['settings'];

            $updatedCount = 0;

            // Les clés du tableau $settings sont les UUIDs (ex: 'settings[UUID][value]')
            foreach ($settings as $uuid => $item) {
                // Vérifie que l'item a un uuid valide et correspond à la clé
                $settingUuid = $item['uuid'] ?? $uuid;

                $setting = Setting::where('uuid', $settingUuid)->first();

                if (! $setting) {
                    continue;
                }

                // Ignore les settings non modifiables (protection côté serveur)
                if (! $setting->is_editable) {
                    continue;
                }

                // Prépare les données de mise à jour
                $updateData = [];

                // is_public : vérifie si la checkbox a été cochée
                // (uniquement si le champ est présent dans le formulaire,
                // sinon on conserve la valeur actuelle en base)
                if (array_key_exists('is_public', $item)) {
                    $updateData['is_public'] = $item['is_public'] === '1';
                }

                // STRING TRADUISIBLE : la vue envoie les traductions par langue (onglets)
                if ($setting->is_translatable && $setting->type === 'string' && isset($item['translations']) && is_array($item['translations'])) {
                    $translations = [];
                    foreach ($item['translations'] as $langId => $translation) {
                        if (isset($translation['lang_id'])) {
                            $translations[] = [
                                'lang_id' => (int) $translation['lang_id'],
                                'value' => $translation['value'] ?? '',
                            ];
                        }
                    }
                    $updateData['translations'] = $translations;
                } elseif (array_key_exists('value', $item)) {
                    // BOOLEAN / INTEGER TRADUISIBLE ou NON TRADUISIBLE : valeur principale
                    $updateData['value'] = $item['value'];
                }

                $this->settingService->backend_settings_update($setting, $updateData);

                $updatedCount++;
            }

            // Log de l'activité de mise à jour réussie
            $this->logActivity(type: 'info', action: 'backend.settings.update', context: ['count' => $updatedCount], message: null, activity: __("{$this->langPath}.update.success", ['count' => $updatedCount]), is_success: true);

            // Redirige vers la page des settings avec un message flash de succès
            return redirect()->route('backend.settings.index')->with('success', __("{$this->langPath}.update.flash_success", ['count' => $updatedCount]));

        } catch (\Exception $e) {

            // Log de l'erreur de mise à jour
            $this->logActivity(type: 'error', action: 'backend.settings.update', context: ['error' => $e->getMessage()], message: $e->getMessage(), activity: __("{$this->langPath}.update.error"), is_success: false);

            // Redirige vers la page des settings avec un message flash d'erreur
            return redirect()->back()->with('error', __("{$this->langPath}.update.flash_error"))->withInput();
        }
    }
}

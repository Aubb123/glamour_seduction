<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Lang;
use App\Services\V1\Controllers\LangService;
use App\Traits\LogActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LangController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/langs/messages/lang-messages-controller.langs';

    // Injection de dépendance du service de gestion des langues
    public function __construct(protected LangService $langService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_langs_index(): View
    {
        try {

            // ✅ Récupère toutes les langues triées par ordre de tri décroissant
            $langs = Lang::orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la liste des langues
            $this->logActivity(type: 'info', action: 'backend.langs.index', context: ['count' => $langs->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Affiche la vue avec les langues récupérées
            return view('v1.backend.pages.langs.index', compact('langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.langs.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des langues
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_langs_activate(Lang $lang): RedirectResponse
    {
        try {

            // Activation de la langue via le service
            $this->langService->activate($lang);

            // Log de l'activation de la langue
            $this->logActivity(type: 'info', action: 'backend.langs.activate', context: ['lang_uuid' => $lang->uuid, 'lang_code' => $lang->code], message: null, activity: __("{$this->langPath}.activate.success", ['name' => $lang->name]), is_success: true, loggable: $lang);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.activate.flash_success", ['name' => $lang->name]));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'activation de la langue
            $this->logActivity(type: 'error', action: 'backend.langs.activate', context: ['lang_uuid' => $lang->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.error", ['uuid' => $lang->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_langs_deactivate(Lang $lang): RedirectResponse
    {
        try {

            // Désactivation de la langue via le service
            $this->langService->deactivate($lang);

            // Log de la désactivation de la langue
            $this->logActivity(type: 'info', action: 'backend.langs.deactivate', context: ['lang_uuid' => $lang->uuid, 'lang_code' => $lang->code], message: null, activity: __("{$this->langPath}.deactivate.success", ['name' => $lang->name]), is_success: true, loggable: $lang);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.deactivate.flash_success", ['name' => $lang->name]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la désactivation
            $this->logActivity(type: 'warning', action: 'backend.langs.deactivate', context: ['lang_uuid' => $lang->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.warning_meta", ['uuid' => $lang->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.langs.deactivate', context: ['lang_uuid' => $lang->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.error", ['uuid' => $lang->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // LANGUAGE SWITCH
    // ─────────────────────────────────────────────
    public function backend_language_switch(Lang $lang): RedirectResponse
    {
        try {

            // Vérifie que la langue est active (le binding implicite ne filtre pas is_active)
            if (! $lang->is_active) {
                $this->logActivity(type: 'warning', action: 'backend.langs.switch', context: ['lang_uuid' => $lang->uuid], message: null, activity: __("{$this->langPath}.switch.warning_meta", ['name' => $lang->name]), is_success: false);

                return back()->with('error', __("{$this->langPath}.switch.warning_meta", ['name' => $lang->name]));
            }

            // Définit la langue dans la session et l'application
            session(['locale' => $lang->code]);
            app()->setLocale($lang->code);

            // Log du changement de langue
            $this->logActivity(type: 'info', action: 'backend.langs.switch', context: ['lang_uuid' => $lang->uuid, 'lang_code' => $lang->code], message: null, activity: __("{$this->langPath}.switch.success", ['name' => $lang->name]), is_success: true, loggable: $lang);

            // Redirection vers la page précédente avec la nouvelle langue
            return redirect()->back();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors du changement de langue
            $this->logActivity(type: 'error', action: 'backend.langs.switch', context: ['lang_uuid' => $lang->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.switch.error", ['uuid' => $lang->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.switch.flash_error"));
        }
    }
}

<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleTranslation;
use App\Services\V1\Controllers\RoleTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleTranslationController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/roles/messages/role-messages-controller.roles.translations';

    public function __construct(
        protected RoleTranslationService $roleTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_roles_translations_destroy(RoleTranslation $translation): RedirectResponse
    {
        try {

            // Traduction résolue par le binding implicite sur uuid (404 automatique si introuvable)

            $this->roleTranslationService->backend_roles_translations_destroy($translation);

            $this->logActivity(type: 'warning', action: 'backend.roles.translations.destroy', context: ['role_translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.roles.translations.destroy', context: ['role_translation_uuid' => $translation->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $translation->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.roles.translations.destroy', context: ['role_translation_uuid' => $translation->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $translation->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_roles_translations_trashed(Role $role): View
    {
        try {

            // Rôle résolu par le binding implicite sur uuid (la route porte ->withTrashed() pour retrouver les rôles supprimés)

            $trashedTranslations = RoleTranslation::onlyTrashed()->where('role_id', $role->id)->get();

            $this->logActivity(type: 'info', action: 'backend.roles.translations.trashed', context: ['count' => $trashedTranslations->count(), 'role_uuid' => $role->uuid], message: null, activity: __("{$this->backendLangPath}.trashed.accessed", ['name' => $role->active_translation?->label ?? 'undefined']), is_success: true, loggable: $role);

            return view('v1.backend.pages.roles.translations.trashed', compact('trashedTranslations', 'role'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.roles.translations.trashed', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error", ['uuid' => $role->uuid]), is_success: false);

            return abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_roles_translations_restore(string $uuid): RedirectResponse
    {
        try {

            $translation = RoleTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->roleTranslationService->backend_roles_translations_restore($translation);

            $this->logActivity(type: 'info', action: 'backend.roles.translations.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.roles.translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.roles.translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.roles.translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_roles_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            $translation = RoleTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->roleTranslationService->backend_roles_translations_force_destroy($translation);

            $this->logActivity(type: 'info', action: 'backend.roles.translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.roles.translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.roles.translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.roles.translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}

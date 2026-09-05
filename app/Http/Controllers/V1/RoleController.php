<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Role\UpdateRoleRequest;
use App\Models\Role;
use App\Models\RoleTranslation;
use App\Services\V1\Controllers\RoleService;
use App\Services\V1\Controllers\RoleTranslationService;
use App\Traits\LogActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/roles/messages/role-messages-controller.roles';

    // Injection de dépendance des services de gestion des rôles
    public function __construct(
        protected RoleService $roleService,
        protected RoleTranslationService $roleTranslationService
    ) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_roles_index(): View
    {
        try {

            // ✅ Récupère toutes les roles avec leurs traductions, triées par date de création décroissante
            $roles = Role::withCount(['audits', 'activityLogs', 'users', 'permissions'])->withActiveTranslation()->orderBy('created_at', 'desc')->get();

            // Log de l'accès à la liste des roles
            $this->logActivity(type: 'info', action: 'backend.roles.index', context: ['count' => $roles->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Affiche la vue avec les roles récupérées
            return view('v1.backend.pages.roles.index', compact('roles'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.roles.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des roles
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveRoleWithSlugCheck(Role $role, string $slug, string $action, string $langKey): Role
    {
        $expectedSlug = $role->slug ?? $role->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['role_uuid' => $role->uuid, 'role_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $role);
            abort(404);
        }

        return $role;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_roles_show(Role $role, string $slug): View
    {
        try {

            // Rôle résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $role = $this->resolveRoleWithSlugCheck($role, $slug, 'backend.roles.show', 'show');

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Charge les compteurs nécessaires à l'affichage
            $role->loadCount(['audits', 'activityLogs', 'users', 'permissions']);

            // Récupère toutes les traductions supprimées (soft deleted) de ce rôle
            $roleTranslations = RoleTranslation::onlyTrashed()->where('role_id', $role->id)->get();

            $roleLabel = isset($role->active_translation) ? $role->active_translation->label : __("{$this->langPath}.show.no_name");

            // Log de la consultation du détail d'un rôle
            $this->logActivity(type: 'info', action: 'backend.roles.show', context: ['role_uuid' => $role->uuid, 'role_slug' => $role->slug], message: null, activity: __("{$this->langPath}.show.success", ['name' => $roleLabel]), is_success: true, loggable: $role);

            // Retourne la vue du rôle avec les données nécessaires
            return view('v1.backend.pages.roles.show', compact('role', 'langs', 'roleTranslations'));

        } catch (\Exception $e) {
            // Log de l'erreur serveur lors de la consultation du détail d'un rôle
            $this->logActivity(type: 'error', action: 'backend.roles.show', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $role->uuid]), is_success: false);
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // PERMISSIONS
    // ─────────────────────────────────────────────
    public function backend_roles_permissions(Role $role, string $slug): View
    {
        try {

            // Rôle résolu par le binding implicite sur uuid
            $role = $this->resolveRoleWithSlugCheck($role, $slug, 'backend.roles.permissions', 'permissions');

            $role->loadCount(['audits', 'activityLogs', 'users', 'permissions']);

            $permissions = $role->permissions()->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.roles.permissions', context: ['role_uuid' => $role->uuid, 'permissions_count' => $permissions->count()], message: null, activity: __("{$this->langPath}.permissions.success", ['name' => $role->active_translation?->label ?? $role->name]), is_success: true, loggable: $role);

            return view('v1.backend.pages.roles.others.permissions', compact('role', 'permissions'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.roles.permissions', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.permissions.error", ['uuid' => $role->uuid]), is_success: false);
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // USERS
    // ─────────────────────────────────────────────
    public function backend_roles_users(Role $role, string $slug): View
    {
        try {

            // Rôle résolu par le binding implicite sur uuid
            $role = $this->resolveRoleWithSlugCheck($role, $slug, 'backend.roles.users', 'users');

            $role->loadCount(['audits', 'activityLogs', 'users', 'permissions']);

            $users = $role->users()->withCount(['activityLogs', 'galleries', 'subscribes', 'audits', 'blogPosts', 'blogPostComments', 'blogPostCommentReplies'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.roles.users', context: ['role_uuid' => $role->uuid, 'users_count' => $users->count()], message: null, activity: __("{$this->langPath}.users.success", ['name' => $role->active_translation?->label ?? $role->name]), is_success: true, loggable: $role);

            return view('v1.backend.pages.roles.others.users', compact('role', 'users'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.roles.users', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.users.error", ['uuid' => $role->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_roles_audits(Role $role, string $slug): View
    {
        try {

            // Rôle résolu par le binding implicite sur uuid
            $role = $this->resolveRoleWithSlugCheck($role, $slug, 'backend.roles.audits', 'audits');

            $role->loadCount(['audits', 'activityLogs', 'users', 'permissions']);

            $audits = $role->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.roles.audits', context: ['role_uuid' => $role->uuid, 'role_slug' => $role->slug], message: null, activity: __("{$this->langPath}.audits.success", ['name' => $role->active_translation?->label ?? $role->name]), is_success: true, loggable: $role);

            return view('v1.backend.pages.roles.others.audits', compact('role', 'audits'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.roles.audits', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $role->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_roles_activity_logs(Role $role, string $slug): View
    {
        try {

            // Rôle résolu par le binding implicite sur uuid
            $role = $this->resolveRoleWithSlugCheck($role, $slug, 'backend.roles.activity-logs', 'activity-logs');

            $role->loadCount(['audits', 'activityLogs', 'users', 'permissions']);

            $logs = $role->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.roles.activity-logs', context: ['role_uuid' => $role->uuid, 'role_slug' => $role->slug], message: null, activity: __("{$this->langPath}.activity-logs.success", ['name' => $role->active_translation?->label ?? $role->name]), is_success: true, loggable: $role);

            return view('v1.backend.pages.roles.others.activity-logs', compact('role', 'logs'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.roles.activity-logs', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $role->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_roles_edit(Role $role): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Rôle résolu par le binding implicite sur uuid (404 automatique si introuvable)
            // Log de l'accès à la page de modification d'un rôle
            $this->logActivity(type: 'info', action: 'backend.roles.edit', context: ['role_uuid' => $role->uuid, 'role_slug' => $role->slug], message: null, activity: __("{$this->langPath}.edit.success", ['name' => $role->active_translation?->label ?? $role->slug]), is_success: true, loggable: $role);

            // Retourne la vue d'édition du rôle avec les données nécessaires
            return view('v1.backend.pages.roles.edit', compact('role', 'langs'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.roles.edit', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.edit.error", ['uuid' => $role->uuid]), is_success: false);
            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_roles_update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        try {

            // Rôle résolu par le binding implicite sur uuid (404 automatique si introuvable)

            // Mise à jour du rôle via le service, en passant les données validées du formulaire
            $this->roleService->update($role, $request->validated());

            // Log de la mise à jour du rôle
            $this->logActivity(type: 'info', action: 'backend.roles.update', context: ['role_uuid' => $role->uuid, 'role_slug' => $role->slug, 'changes' => $role->getChanges()], message: null, activity: __("{$this->langPath}.update.success", ['name' => $role->active_translation?->label ?? $role->slug]), is_success: true, loggable: $role);

            // Redirection vers la page de détail du rôle avec un message de succès
            return redirect()->route('backend.roles.show', ['role' => $role->uuid, 'slug' => $role->active_translation?->slug ?? 'null'])->with('success', __("{$this->langPath}.update.flash_success", ['name' => $role->active_translation?->label ?? $role->slug]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la mise à jour du rôle
            $this->logActivity(type: 'warning', action: 'backend.roles.update', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning_meta", ['uuid' => $role->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour du rôle
            $this->logActivity(type: 'error', action: 'backend.roles.update', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.error", ['uuid' => $role->uuid]), is_success: false);

            // Redirection avec un message d'erreur
            return back()->with('error', __("{$this->langPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_roles_activate(Role $role): RedirectResponse
    {
        try {

            // Rôle résolu par le binding implicite sur uuid (404 automatique si introuvable)

            // Activation du rôle via le service
            $this->roleService->activate($role);

            // Log de l'activation du rôle
            $this->logActivity(type: 'info', action: 'backend.roles.activate', context: ['role_uuid' => $role->uuid, 'role_slug' => $role->slug], message: null, activity: __("{$this->langPath}.activate.success", ['name' => $role->active_translation?->label ?? $role->slug]), is_success: true, loggable: $role);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.activate.flash_success", ['name' => $role->active_translation?->label ?? $role->slug]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de l'activation
            $this->logActivity(type: 'warning', action: 'backend.roles.activate', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.warning_meta", ['uuid' => $role->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.roles.activate', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.error", ['uuid' => $role->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_roles_deactivate(Role $role): RedirectResponse
    {
        try {

            // Rôle résolu par le binding implicite sur uuid (404 automatique si introuvable)

            // Désactivation du rôle via le service
            $this->roleService->deactivate($role);

            // Log de la désactivation du rôle
            $this->logActivity(type: 'info', action: 'backend.roles.deactivate', context: ['role_uuid' => $role->uuid, 'role_slug' => $role->slug], message: null, activity: __("{$this->langPath}.deactivate.success", ['name' => $role->active_translation?->label ?? $role->slug]), is_success: true, loggable: $role);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.deactivate.flash_success", ['name' => $role->active_translation?->label ?? $role->slug]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la désactivation
            $this->logActivity(type: 'warning', action: 'backend.roles.deactivate', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.warning_meta", ['uuid' => $role->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.roles.deactivate', context: ['role_uuid' => $role->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.error", ['uuid' => $role->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_roles_trashed(): View
    {
        try {

            $roles = Role::onlyTrashed()->withTrashedActiveTranslation()->withCount(['audits', 'activityLogs', 'users', 'permissions'])->orderBy('deleted_at', 'desc')->get();

            // Log de la consultation de la corbeille des roles
            $this->logActivity(type: 'info', action: 'backend.roles.trashed', context: ['count' => $roles->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.roles.trashed', compact('roles'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de la consultation de la corbeille des roles
            $this->logActivity(type: 'error', action: 'backend.roles.trashed', context: ['exception' => get_class($e)], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }
}

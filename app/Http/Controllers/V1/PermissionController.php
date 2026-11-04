<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Permission\StorePermissionRequest;
use App\Http\Requests\V1\Permission\SyncRolesPermissionRequest;
use App\Http\Requests\V1\Permission\UpdatePermissionRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\V1\Controllers\PermissionService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

// Alias pour les chemins de traduction
use function __;

class PermissionController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/permissions/messages/permission-messages-controller.permissions';

    // Injection de dépendance du service de gestion des permissions
    public function __construct(protected PermissionService $permissionService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_permissions_index(): View
    {
        try {

            // ✅ Récupère toutes les permissions, triées par date de création décroissante
            $permissions = Permission::withCount(['roles', 'audits', 'activityLogs'])->orderBy('created_at', 'desc')->get();

            // Log de l'accès à la liste des permissions
            $this->logActivity(type: 'info', action: 'backend.permissions.index', context: ['count' => $permissions->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les permissions récupérées
            return view('v1.backend.pages.permissions.index', compact('permissions'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.permissions.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des permissions
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_permissions_create(): View
    {
        try {

            $roles = Role::orderBy('name', 'asc')->get(); // Récupère tous les rôles pour l'assignation

            // Log de l'accès à la page de création d'une permission
            $this->logActivity(type: 'info', action: 'backend.permissions.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            // Affiche la vue de création d'une permission
            return view('v1.backend.pages.permissions.create', compact('roles'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'une permission
            $this->logActivity(type: 'error', action: 'backend.permissions.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de l'accès à la page de création
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_permissions_store(StorePermissionRequest $request): RedirectResponse
    {
        try {

            // Création de la permission via le service, en passant les données validées du formulaire
            $permission = $this->permissionService->backend_permissions_store($request->validated());

            // Log de la création de la permission avec le nom de la permission créée
            $this->logActivity(type: 'info', action: 'backend.permissions.store', context: ['permission_uuid' => $permission->uuid, 'permission_name' => $permission->name], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $permission->name]), is_success: true, loggable: $permission);

            // Redirection vers la liste des permissions avec un message de succès
            return redirect()->route('backend.permissions.show', ['permission' => $permission->uuid, 'slug' => $permission->slug])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $permission->name]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création de la permission
            $this->logActivity(type: 'warning', action: 'backend.permissions.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création de la permission
            $this->logActivity(type: 'error', action: 'backend.permissions.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_permissions_show(Permission $permission, string $slug): View
    {
        try {

            // Permission résolue par le binding implicite sur uuid (404 automatique si introuvable)
            $expectedSlug = $permission->slug ?? $permission->active_translation?->slug ?? 'null';

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.permissions.show', context: ['permission_uuid' => $permission->uuid, 'permission_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.show.slug_mismatch"), is_success: false, loggable: $permission);

                abort(404); // Slug incohérent
            }

            $permission->loadCount(['roles', 'audits', 'activityLogs']);

            $permissionName = $permission->name;

            // Log de la consultation du détail d'une permission
            $this->logActivity(type: 'info', action: 'backend.permissions.show', context: ['permission_uuid' => $permission->uuid, 'permission_name' => $permission->name], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $permissionName]), is_success: true, loggable: $permission);

            // Retourne la vue de la permission avec les données nécessaires
            return view('v1.backend.pages.permissions.show', compact('permission'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'une permission
            $this->logActivity(type: 'error', action: 'backend.permissions.show', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $permission->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la permission
        }
    }

    // ─────────────────────────────────────────────
    // ROLES
    // ─────────────────────────────────────────────
    public function backend_permissions_roles(Permission $permission, string $slug): View
    {
        try {

            // Permission résolue par le binding implicite sur uuid
            $expectedSlug = $permission->slug ?? $permission->active_translation?->slug ?? 'null';

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.permissions.roles', context: ['permission_uuid' => $permission->uuid, 'permission_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.roles.slug_mismatch"), is_success: false, loggable: $permission);

                abort(404);
            }

            $permission->loadCount(['roles', 'audits', 'activityLogs']);

            $roles = $permission->roles()->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.permissions.roles', context: ['permission_uuid' => $permission->uuid, 'roles_count' => $roles->count()], message: null, activity: __("{$this->backendLangPath}.roles.success", ['name' => $permission->name]), is_success: true, loggable: $permission);

            return view('v1.backend.pages.permissions.others.roles', compact('permission', 'roles'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.permissions.roles', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.roles.error", ['uuid' => $permission->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_permissions_audits(Permission $permission, string $slug): View
    {
        try {

            // Permission résolue par le binding implicite sur uuid
            $expectedSlug = $permission->slug ?? $permission->active_translation?->slug ?? 'null';

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.permissions.audits', context: ['permission_uuid' => $permission->uuid, 'permission_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.audits.slug_mismatch"), is_success: false, loggable: $permission);

                abort(404);
            }

            $permission->loadCount(['roles', 'audits', 'activityLogs']);

            $audits = $permission->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.permissions.audits', context: ['permission_uuid' => $permission->uuid, 'permission_name' => $permission->name], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $permission->name]), is_success: true, loggable: $permission);

            return view('v1.backend.pages.permissions.others.audits', compact('permission', 'audits'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.permissions.audits', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $permission->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_permissions_activity_logs(Permission $permission, string $slug): View
    {
        try {

            // Permission résolue par le binding implicite sur uuid
            $expectedSlug = $permission->slug ?? $permission->active_translation?->slug ?? 'null';

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.permissions.activity-logs', context: ['permission_uuid' => $permission->uuid, 'permission_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.activity-logs.slug_mismatch"), is_success: false, loggable: $permission);

                abort(404);
            }

            $permission->loadCount(['roles', 'audits', 'activityLogs']);

            $logs = $permission->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.permissions.activity-logs', context: ['permission_uuid' => $permission->uuid, 'permission_name' => $permission->name], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $permission->name]), is_success: true, loggable: $permission);

            return view('v1.backend.pages.permissions.others.activity-logs', compact('permission', 'logs'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.permissions.activity-logs', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $permission->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_permissions_edit(Permission $permission): View
    {
        try {

            // Permission résolue par le binding implicite sur uuid (404 automatique si introuvable)

            $roles = Role::orderBy('name', 'asc')->get(); // Récupère tous les rôles pour l'assignation

            // Log de l'accès à la page de modification d'une permission
            $this->logActivity(type: 'info', action: 'backend.permissions.edit', context: ['permission_uuid' => $permission->uuid, 'permission_name' => $permission->name], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $permission->name]), is_success: true, loggable: $permission);

            // Retourne la vue d'édition de la permission avec les données nécessaires
            return view('v1.backend.pages.permissions.edit', compact('permission', 'roles'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de modification d'une permission
            $this->logActivity(type: 'error', action: 'backend.permissions.edit', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $permission->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la permission
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_permissions_update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        try {

            // Permission résolue par le binding implicite sur uuid (404 automatique si introuvable)

            // Mise à jour de la permission via le service, en passant les données validées du formulaire
            $this->permissionService->backend_permissions_update($permission, $request->validated());

            // Récupère une instance fraîche pour capturer les vrais changements
            $permission->refresh();

            // Log de la mise à jour de la permission avec le uuid et les changements effectués
            $this->logActivity(type: 'info', action: 'backend.permissions.update', context: ['permission_uuid' => $permission->uuid, 'permission_name' => $permission->name, 'changes' => $permission->getChanges()], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $permission->name]), is_success: true, loggable: $permission);

            // Redirection vers la page de détail de la permission avec un message de succès
            return redirect()->route('backend.permissions.show', ['permission' => $permission->uuid, 'slug' => $permission->slug])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $permission->name]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la mise à jour de la permission
            $this->logActivity(type: 'warning', action: 'backend.permissions.update', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $permission->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour de la permission
            $this->logActivity(type: 'error', action: 'backend.permissions.update', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $permission->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de la mise à jour, en conservant les données du formulaire
            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_permissions_activate(Permission $permission): RedirectResponse
    {
        try {

            // Permission résolue par le binding implicite sur uuid (404 automatique si introuvable)

            // Activation de la permission via le service
            $this->permissionService->backend_permissions_activate($permission);

            // Log de l'activation de la permission avec le nom de la permission activée
            $this->logActivity(type: 'info', action: 'backend.permissions.activate', context: ['permission_uuid' => $permission->uuid, 'permission_name' => $permission->name], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $permission->name]), is_success: true, loggable: $permission);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $permission->name]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de l'activation de la permission
            $this->logActivity(type: 'warning', action: 'backend.permissions.activate', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $permission->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'activation de la permission
            $this->logActivity(type: 'error', action: 'backend.permissions.activate', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de l'activation de la permission
            return back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_permissions_deactivate(Permission $permission): RedirectResponse
    {
        try {

            // Permission résolue par le binding implicite sur uuid (404 automatique si introuvable)

            // Désactivation de la permission via le service
            $this->permissionService->backend_permissions_deactivate($permission);

            // Log de la désactivation de la permission avec le nom de la permission désactivée
            $this->logActivity(type: 'info', action: 'backend.permissions.deactivate', context: ['permission_uuid' => $permission->uuid, 'permission_name' => $permission->name], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $permission->name]), is_success: true, loggable: $permission);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $permission->name]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la désactivation de la permission
            $this->logActivity(type: 'warning', action: 'backend.permissions.deactivate', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $permission->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la désactivation de la permission
            $this->logActivity(type: 'error', action: 'backend.permissions.deactivate', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de la désactivation de la permission
            return back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // ASSIGN TO ROLE (page d'assignation)
    // ─────────────────────────────────────────────
    public function backend_permissions_assign(Permission $permission): View
    {
        try {

            // Permission résolue par le binding implicite sur uuid (404 automatique si introuvable)

            $roles = Role::orderBy('name', 'asc')->get(); // Récupère tous les rôles pour l'assignation

            // Log de l'accès à la page d'assignation de la permission
            $this->logActivity(type: 'info', action: 'backend.permissions.assign', context: ['permission_uuid' => $permission->uuid, 'permission_name' => $permission->name], message: null, activity: __("{$this->backendLangPath}.assign.success", ['name' => $permission->name]), is_success: true, loggable: $permission);

            // Retourne la vue d'assignation de la permission avec les données nécessaires
            return view('v1.backend.pages.permissions.assign', compact('permission', 'roles'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page d'assignation de la permission
            $this->logActivity(type: 'error', action: 'backend.permissions.assign', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.assign.error", ['uuid' => $permission->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la permission
        }
    }

    // ─────────────────────────────────────────────
    // SYNC ROLES (enregistrer l'assignation)
    // ─────────────────────────────────────────────
    public function backend_permissions_sync_roles(SyncRolesPermissionRequest $request, Permission $permission): RedirectResponse
    {
        try {

            // Permission résolue par le binding implicite sur uuid (404 automatique si introuvable)

            $roleIds = $request->input('role_ids', []);

            // Synchronisation des rôles via le service
            $this->permissionService->backend_permissions_sync_roles($permission, $roleIds);

            // Log de la synchronisation des rôles de la permission
            $this->logActivity(type: 'info', action: 'backend.permissions.sync_roles', context: ['permission_uuid' => $permission->uuid, 'permission_name' => $permission->name, 'roles_count' => count($roleIds)], message: null, activity: __("{$this->backendLangPath}.sync_roles.success", ['name' => $permission->name]), is_success: true, loggable: $permission);

            // Redirection vers la page de détail de la permission avec un message de succès
            return redirect()->route('backend.permissions.show', ['permission' => $permission->uuid, 'slug' => $permission->slug])->with('success', __("{$this->backendLangPath}.sync_roles.flash_success"));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la synchronisation des rôles de la permission
            $this->logActivity(type: 'error', action: 'backend.permissions.sync_roles', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.sync_roles.error", ['uuid' => $permission->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.sync_roles.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_permissions_trashed(): View
    {
        try {
            $permissions = Permission::onlyTrashed()->withCount(['roles', 'audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.permissions.trashed', context: ['count' => $permissions->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.permissions.trashed', compact('permissions'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.permissions.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_permissions_destroy(Permission $permission): RedirectResponse
    {
        try {

            // Permission résolue par le binding implicite sur uuid (404 automatique si introuvable)

            $this->permissionService->backend_permissions_destroy($permission);

            $this->logActivity(type: 'info', action: 'backend.permissions.destroy', context: ['permission_uuid' => $permission->uuid, 'permission_name' => $permission->name], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $permission->name]), is_success: true, loggable: $permission);

            return redirect()->route('backend.permissions.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $permission->name]));

        } catch (\InvalidArgumentException $e) {
            $this->logActivity(type: 'warning', action: 'backend.permissions.destroy', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $permission->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.permissions.destroy', context: ['permission_uuid' => $permission->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $permission->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_permissions_restore(string $uuid): RedirectResponse
    {
        try {
            $permission = $this->permissionService->backend_permissions_restore($uuid);

            $this->logActivity(type: 'info', action: 'backend.permissions.restore', context: ['permission_uuid' => $permission->uuid, 'permission_name' => $permission->name], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $permission->name]), is_success: true, loggable: $permission);

            return redirect()->route('backend.permissions.show', ['permission' => $permission->uuid, 'slug' => $permission->slug])->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $permission->name]));

        } catch (ModelNotFoundException $e) {
            $this->logActivity(type: 'warning', action: 'backend.permissions.restore', context: ['permission_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return redirect()->route('backend.permissions.trashed')->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {
            $this->logActivity(type: 'warning', action: 'backend.permissions.restore', context: ['permission_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.permissions.restore', context: ['permission_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_permissions_force_destroy(string $uuid): RedirectResponse
    {
        try {
            $this->permissionService->backend_permissions_force_destroy($uuid);

            $this->logActivity(type: 'info', action: 'backend.permissions.force_destroy', context: ['permission_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true);

            return redirect()->route('backend.permissions.trashed')->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {
            $this->logActivity(type: 'warning', action: 'backend.permissions.force_destroy', context: ['permission_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return redirect()->route('backend.permissions.trashed')->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.permissions.force_destroy', context: ['permission_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"))->withInput();
        }
    }
}

<?php

namespace App\Http\Middleware\Web\V1;

use App\Enums\Permissions\PermissionStatus;
use App\Enums\Roles\RoleStatus;
use App\Traits\LogActivity;
use Closure; // Trait pour le logging d'activité
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function __;

class CheckActiveRole
{
    use LogActivity; // Trait pour le logging d'activité

    protected string $backendLangPath = 'v1/backend/others/middlewares/messages-middleware.checkActiveRole';

    /**
     * Handle an incoming request.
     *
     * Spatie résout les permissions par leur nom uniquement : il ignore nos
     * colonnes custom "status" sur roles et permissions. Ce middleware
     * applique donc sémantique d'activation :
     *
     *  1. Seuls les rôles ACTIFS de l'utilisateur sont considérés ;
     *  2. Une permission n'est effective que si elle est ACTIVE et portée
     *     par un rôle actif (ou attribuée directement à l'utilisateur) ;
     *  3. Sans aucune permission effective accessible → accès refusé.
     *
     * NB : le middleware ne remplace pas le cache Spatie — pensez à purger
     * le registrar (forgetCachedPermissions) lors des activations/désactivations.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {

            // Récupère TOUS les rôles de l'utilisateur avec leurs permissions
            // (les rôles soft-deleted sont exclus par le scope par défaut de SoftDeletes)
            $roles = $user->roles()->with('permissions')->get();

            // Exclut les rôles inactifs : seuls les rôles actifs confèrent des droits
            $activeRoles = $roles->filter(fn ($role) => $role->status === RoleStatus::Active);

            // Une permission n'est effective que si elle est ACTIVE et portée par un rôle actif
            $hasPermissionViaActiveRole = $activeRoles->contains(
                fn ($role) => $role->permissions->contains(fn ($permission) => $permission->status === PermissionStatus::Active)
            );

            // Permissions directes de l'utilisateur (model_has_permissions) : uniquement les actives
            $hasDirectActivePermission = $user->permissions()
                ->where('status', PermissionStatus::Active)
                ->exists();

            // Aucun rôle actif du tout
            if ($activeRoles->isEmpty() && ! $hasDirectActivePermission) {

                return $this->denyAccess($user, 'no_active_role');
            }

            // Des rôles actifs existent, mais aucune permission active n'est accessible
            // (toutes les permissions des rôles actifs sont inactives)
            if (! $hasPermissionViaActiveRole && ! $hasDirectActivePermission) {

                return $this->denyAccess($user, 'no_active_permission');
            }
        }

        return $next($request);
    }

    /**
     * Refuse l'accès : log, déconnexion et redirection vers le login.
     */
    private function denyAccess($user, string $reason): Response
    {
        // Log de l'accès refusé en raison d'un rôle/permission inactif
        $this->logActivity(type: 'warning', action: 'middleware.checkActiveRole', context: ['user_id' => $user->id, 'user_email' => $user->email, 'reason' => $reason], message: null, activity: "{$this->backendLangPath}.{$reason}", is_success: false, loggable: $user);

        // Si aucun rôle actif, on déconnecte ou on refuse l'accès
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login')->with('error', __("{$this->backendLangPath}.{$reason}"));
    }
}

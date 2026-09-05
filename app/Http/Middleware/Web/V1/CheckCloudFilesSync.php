<?php

namespace App\Http\Middleware\Web\V1;

use App\Enums\Clouds\Files\CloudFileStatus;
use App\Enums\Clouds\Platforms\CloudPlatformStatus;
use App\Models\CloudFile;
use App\Models\CloudPlatform;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

use function __;

class CheckCloudFilesSync
{
    /**
     * Chemin vers les messages de langue pour ce middleware.
     */
    protected string $langPath = 'v1/backend/others/middlewares/messages-middleware.checkCloudFilesSync';

    /**
     * Rôles autorisés à être redirigés vers la page de synchronisation.
     *
     * @var array<int, string>
     */
    protected array $allowedRoles = ['administrator', 'developer', 'moderator', 'support'];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Récupère l'utilisateur authentifié
        $user = $request->user();

        // Récupère la valeur du setting "max_unsynced_files_before_redirect" pour déterminer le seuil.
        // La valeur peut être absente (null) ou invalide : on retombe sur un défaut de 10,
        // et on garantit un seuil d'au moins 1 pour éviter une redirection systématique.
        $threshold = max(1, (int) (getSettingValue('max_unsynced_files_before_redirect', app()->getLocale()) ?? 10));

        // L'utilisateur doit être connecté
        if (! $user) {
            return $next($request);
        }

        // L'utilisateur doit avoir l'un des rôles autorisés
        if (! $user->hasAnyRole($this->allowedRoles)) {
            return $next($request);
        }

        // Ne pas rediriger si l'utilisateur est déjà sur la page de synchronisation (évite une boucle infinie)
        if ($request->route()?->named('backend.cloud-files.sync')) {
            return $next($request);
        }

        // Ne pas rediriger la route proxy qui sert les fichiers cloud (elle doit rester accessible)
        if ($request->route()?->named('cloud-file.show')) {
            return $next($request);
        }

        // Ne pas rediriger les requêtes non-GET (POST, PUT, DELETE, etc.) pour ne pas perdre la soumission
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        // Ne pas rediriger les requêtes AJAX ou attendues en JSON
        if ($request->ajax() || $request->expectsJson()) {
            return $next($request);
        }

        // Compte les fichiers cloud non synchronisés (pending/error) rattachés
        // à une plateforme ACTIVE. Les fichiers en "processing" sont EXCLUS : ils
        // sont en cours de traitement (ou seront réinitialisés par l'expiration
        // "processing" de la commande cloud:sync). Mis en cache 60s pour éviter
        // un scan complet de la table à chaque requête.
        // La clé de cache intègre les plateformes actives pour invalider quand on
        // active/désactive une plateforme.
        $activePlatformKey = CloudPlatform::where('status', CloudPlatformStatus::Active->value)->orderBy('id')->pluck('id')->implode(',');

        $nonSyncedCount = Cache::remember(
            'cloud_files_non_synced_count.'.$activePlatformKey,
            60, // 60 secondes : limite raisonnable entre fraîcheur et surcharge requêtes
            function () {
                return CloudFile::whereHas('cloudFolder.platform', function ($query) {
                    $query->where('status', CloudPlatformStatus::Active->value);
                })
                    ->whereIn('sync_status', [CloudFileStatus::Pending, CloudFileStatus::Error])
                    ->count();
            }
        );

        // Si le nombre de fichiers non synchronisés atteint le seuil, redirige vers la page de synchronisation
        if ($nonSyncedCount >= $threshold) {
            // Redirige vers la page de synchronisation avec un message d'avertissement
            return redirect()->route('backend.cloud-files.sync')->with('warning', __("{$this->langPath}.non_synced_files", ['count' => $nonSyncedCount]));
        }

        return $next($request);
    }
}

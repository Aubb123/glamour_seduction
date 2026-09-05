<?php

namespace App\Services\V1;

use App\Enums\PlatformVersions\PlatformVersionStatus;
use App\Models\PlatformVersion;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\Schema;

class VersionService
{
    use LogActivity;

    public function getActive(): ?PlatformVersion
    {
        // Protection si la table n'existe pas encore (ex: première migration)
        if (! Schema::hasTable('platform_versions')) {
            return null;
        }

        // ✅ Vérifie qu'une version active et publiée existe.
        // Priorité : la version marquée "is_default" (la v1 par défaut) d'abord,
        // puis la plus récente (created_at desc) si aucune par défaut n'est désignée.
        // NB : plusieurs versions peuvent être actives en même temps (multi-actif assumé),
        // la version "courante" exposée au front est donc celle résolue ici.
        $query = PlatformVersion::query();

        // Protection : la colonne `status` peut ne pas exister pendant une migration
        // (avant que la conversion is_active → status soit exécutée), ou sur une base
        // encore à l'ancien schéma. Dans ce cas on filtre sur l'ancienne colonne is_active.
        if (Schema::hasColumn('platform_versions', 'status')) {
            $query->where('status', PlatformVersionStatus::Active);
        } else {
            // // Ancienne colonne is_active (booléen) : on filtre sur true
            // $query->where('is_active', true); // ← ancienne colonne is_active (booléen)
        }

        $query->where('is_published', true)
            ->whereNotNull('released_at');

        // Protection : si la colonne is_default n'existe pas encore (ex: pendant une
        // migration, au boot de l'app), on ne trie pas dessus pour éviter un crash.
        if (Schema::hasColumn('platform_versions', 'is_default')) {
            $query->orderByDesc('is_default');
        }

        $version = $query->orderByDesc('created_at')->first();

        if (! $version) {
            $this->logActivity(type: 'warning', action: 'apply_version', context: [], message: 'Aucune version publiée trouvée', activity: 'Aucune version publiée trouvée dans VersionService::getActive()', is_success: false
            );

            if (app()->runningInConsole()) {
                return null;
            }

            dd('Aucune version publiée trouvée. Veuillez vérifier la configuration de votre application.');
            abort(404, 'Aucune version publiée trouvée.');
        }

        // Log de la version appliquée
        // $this->logActivity(type: 'info', action: 'apply_version', context: ['version_code' => $version->code], message: 'Version appliquée', activity: 'Version appliquée dans VersionService::getActive()', is_success: true, loggable: $version);

        return $version;
    }
}

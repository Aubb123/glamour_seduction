<?php

namespace Database\Seeders;

use App\Enums\Clouds\Platforms\CloudPlatformStatus;
use App\Models\CloudPlatform;
use App\Services\V1\Controllers\CloudPlatformService;
use Illuminate\Database\Seeder;

class CloudFolderSeeder extends Seeder
{
    public function __construct(protected CloudPlatformService $cloudPlatformService) {}

    public function run(): void
    {
        $cloudPlatform = CloudPlatform::where('status', CloudPlatformStatus::Active->value)->first();

        // Aucune plateforme active : on n'essaie pas de créer l'architecture
        // (évite un crash sur ->status d'une instance nulle).
        if (! $cloudPlatform) {
            $this->command?->warn('Aucune plateforme cloud active trouvée : architecture non créée.');

            return;
        }

        $this->cloudPlatformService->createArchitecture($cloudPlatform);
    }
}

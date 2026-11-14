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

        // If no cloud provider is configured yet, keep seeding safe and non-fatal.
        if (! $cloudPlatform) {
            $this->command?->warn('No active cloud platform found; cloud folder architecture was not created.');
            return;
        }

        $this->cloudPlatformService->backend_cloud_platforms_architecture_store($cloudPlatform);
    }
}

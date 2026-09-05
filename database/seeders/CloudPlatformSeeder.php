<?php

namespace Database\Seeders;

use App\Models\CloudPlatform;
use Illuminate\Database\Seeder;

class CloudPlatformSeeder extends Seeder
{
    public function run(): void
    {
        // ===== CloudPlat - Backblaze B2 =====
        CloudPlatform::create([
            'name' => 'backblaze_b2',
            'disk_name' => 'b2',
            'label' => 'Backblaze B2 Cloud Storage',
            'api_endpoint' => config('filesystems.disks.b2.endpoint') ?? 'https://s3.us-west-004.backblazeb2.com',
            'api_key' => null, // Secrets gérés uniquement via config/filesystems.php (driver b2)
            'api_secret' => null,
            'description' => 'Backblaze B2 est un stockage d\'objets cloud public avec une API compatible S3, offrant des prix compétitifs et une intégration facile avec Laravel.',
            'metadata' => [
                'bucket' => config('filesystems.disks.b2.bucket'),
                'region' => config('filesystems.disks.b2.region') ?? 'us-west-004',
                'storage_limit' => '10GB',
                'features' => ['s3_compatible_api', 'bandwidth_alliance', 'object_lock', 'lifecycle_rules'],
            ],
            'status' => 'active',
        ]);
    }
}

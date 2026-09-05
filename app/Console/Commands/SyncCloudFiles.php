<?php

namespace App\Console\Commands;

use App\Enums\Clouds\Files\CloudFileStatus;
use App\Models\CloudFile;
use App\Services\Cloud\CloudStorageService;
use Illuminate\Console\Command;

class SyncCloudFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloud:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migre les fichiers cloud en attente (pending/error) vers Backblaze B2';

    /**
     * Execute the console command.
     */
    public function handle(CloudStorageService $cloudStorage): int
    {
        // 1. Nettoyage : repasse en "pending" les fichiers restés bloqués en "processing"
        //    depuis plus de X minutes (setting "cloudfiles.reset_stuck_processing_after_minutes").
        $resetCount = CloudFile::resetStuckProcessing();

        if ($resetCount > 0) {
            $this->line("  ↻ {$resetCount} fichier(s) bloqué(s) en 'processing' repassés en 'pending'.");
        }

        // 2. Récupère les fichiers éligibles à une migration (retries bornés via le setting
        //    "cloudfiles.retry_sync_after_minutes") : seuls les fichiers en "pending"/"error"
        //    dont la dernière tentative est absente ou plus vieille que X minutes sont traités.
        $pendingFiles = CloudFile::retryable()
            ->whereNotNull('local_path')
            ->get();

        $count = $pendingFiles->count();

        if ($count === 0) {
            $this->info('Aucun fichier à migrer.');

            return self::SUCCESS;
        }

        $this->info("{$count} fichier(s) à migrer vers B2...");

        $success = 0;
        $failed = 0;

        foreach ($pendingFiles as $cloudFile) {
            // Marquer comme "processing" avant la migration
            $cloudFile->update([
                'sync_status' => CloudFileStatus::Processing,
                'last_sync_attempt_at' => getDateTime(),
            ]);

            if ($cloudStorage->migrateToCloud($cloudFile)) {
                $success++;
                $this->line("  ✓ {$cloudFile->file_name} migré vers B2");
            } else {
                $failed++;
                $this->error("  ✗ {$cloudFile->file_name} échec de migration");
            }
        }

        $this->info("Terminé : {$success} migré(s), {$failed} en échec.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}

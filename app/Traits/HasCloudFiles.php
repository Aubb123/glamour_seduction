<?php

namespace App\Traits;

use App\Models\CloudFile;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCloudFiles
{
    /**
     * Relation polymorphique vers les fichiers cloud.
     *
     * Un CloudFile ne peut pas avoir ses propres fichiers cloud attachés :
     * la relation est neutralisée (toujours vide) pour éviter toute confusion.
     */
    public function cloudFiles(): MorphMany
    {
        $relation = $this->morphMany(CloudFile::class, 'fileable');

        if ($this instanceof CloudFile) {
            $relation->whereRaw('1 = 0');
        }

        return $relation;
    }

    /**
     * Récupère la première image associée au modèle
     */
    public function getFirstImage()
    {
        $cloudFile = $this->cloudFiles->firstWhere('file_type', 'image');

        if ($cloudFile) {
            $url_img = route('cloud-file.show', ['uuid' => $cloudFile->uuid]);

            return [
                'url_img' => $url_img,
                'cloud_file' => $cloudFile,
                'cloud_folder' => $cloudFile->cloudFolder,
                'cloud_platform' => $cloudFile->cloudFolder->platform,
            ];
        }

        return [
            'url_img' => asset(getEnvFolder().'others/all/assets/images/default.jpg'),
            'cloud_file' => null,
            'cloud_folder' => null,
            'cloud_platform' => null,
        ];
    }

    /**
     * Vérifie si le modèle a une image de couverture
     */
    public function hasCoverImage(): bool
    {
        return $this->hasCloudFileType('image');
    }

    /**
     * Vérifie si le modèle a au moins un fichier cloud d'un type donné
     */
    public function hasCloudFileType(string $fileType): bool
    {
        return $this->cloudFiles()->where('file_type', $fileType)->exists();
    }
}

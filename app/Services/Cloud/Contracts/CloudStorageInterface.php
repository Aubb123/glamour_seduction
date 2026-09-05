<?php

namespace App\Services\Cloud\Contracts;

use Illuminate\Http\UploadedFile;

interface CloudStorageInterface
{
    /**
     * Upload un fichier vers le stockage cloud
     */
    public function upload(string $path, UploadedFile $file, array $options = []): string;

    /**
     * Upload un fichier en utilisant putFile (nom généré automatiquement)
     */
    public function putFile(string $path, UploadedFile $file, array $options = []): string;

    /**
     * Récupère l'URL publique d'un fichier
     */
    public function url(string $path): string;

    /**
     * Récupère une URL temporairement signée
     */
    public function temporaryUrl(string $path, \DateTimeInterface $expiration): string;

    /**
     * Télécharge un fichier
     */
    public function download(string $path);

    /**
     * Vérifie si un fichier existe
     */
    public function exists(string $path): bool;

    /**
     * Supprime un fichier
     */
    public function delete(string $path): bool;

    /**
     * Déplace un fichier
     */
    public function move(string $from, string $to): bool;

    /**
     * Copie un fichier
     */
    public function copy(string $from, string $to): bool;

    /**
     * Liste les fichiers d'un répertoire
     */
    public function files(string $directory): array;

    /**
     * Liste les sous-répertoires d'un répertoire
     */
    public function directories(string $directory): array;
}

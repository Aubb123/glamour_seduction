<?php

namespace App\Enums\Clouds\Files;

use function __;

/**
 * Type de fichier cloud (source unique de vérité pour le typage des fichiers).
 *
 * Les valeurs DOIVENT correspondre strictement à l'énum MySQL de la colonne
 * `cloud_files.file_type` : video, image, zip, others — ni plus ni moins.
 */
enum CloudFileType: string
{
    case Video = 'video';
    case Image = 'image';
    case Zip = 'zip';
    case Others = 'others';

    /**
     * Détermine le type de fichier à partir de son extension.
     */
    public static function fromExtension(string $extension): self
    {
        return match (strtolower($extension)) {
            'jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg', 'avif', 'ico' => self::Image,
            'mp4', 'm4v', 'mov', 'avi', 'mkv', 'webm', 'mpeg', 'mpg' => self::Video,
            'zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz', 'tgz' => self::Zip,
            default => self::Others,
        };
    }

    /**
     * Vérifie que le type MIME détecté est cohérent avec le type de fichier.
     *
     * Les types MIME "ambigus" (ex : application/octet-stream) ne sont pas
     * pénalisés : l'extension fait alors foi. On bloque uniquement les
     * contradictions évidentes (ex : une image servant un MIME video).
     */
    public static function matchesMime(self $fileType, ?string $mime): bool
    {
        if ($mime === null) {
            return true;
        }

        $mimeCategory = match (true) {
            str_starts_with($mime, 'image/') => self::Image,
            str_starts_with($mime, 'video/') => self::Video,
            str_starts_with($mime, 'audio/') => self::Others,
            default => null, // MIME ambigu : on ne bloque pas, l'extension fait foi.
        };

        if ($mimeCategory === null) {
            return true;
        }

        return $mimeCategory === $fileType;
    }

    /**
     * Libellé lisible du type de fichier (ex : VIDÉO, IMAGE, ARCHIVE ZIP, AUTRE).
     */
    public function label(): string
    {
        return match ($this) {
            self::Image => __('Image'),
            self::Video => __('Vidéo'),
            self::Zip => __('Archive ZIP'),
            self::Others => __('Autre'),
        };
    }

    /**
     * Classe CSS du badge associé au type de fichier.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Image => 'badge-soft-success',
            self::Video => 'badge-soft-info',
            self::Zip => 'badge-soft-warning',
            self::Others => 'badge-soft-secondary',
        };
    }
}

<?php

namespace App\Services\Cloud\Drivers;

use App\Services\Cloud\Contracts\CloudStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BackblazeDriver implements CloudStorageInterface
{
    public function __construct(
        protected string $disk,
    ) {}

    public function upload(string $path, UploadedFile $file, array $options = []): string
    {
        // Ouvre le fichier en mode lecture (stream) pour un upload optimisé en mémoire
        $stream = fopen($file->getRealPath(), 'r+');

        try {
            $result = Storage::disk($this->disk)->put($path, $stream, $options);
        } finally {
            // Toujours fermer le flux ouvert, même en cas d'erreur, pour éviter la fuite de descripteur de fichier
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        return $result;
    }

    public function putFile(string $path, UploadedFile $file, array $options = []): string
    {
        return Storage::disk($this->disk)->putFile($path, $file, $options);
    }

    public function url(string $path): string
    {
        return Storage::disk($this->disk)->url($path);
    }

    public function temporaryUrl(string $path, \DateTimeInterface $expiration): string
    {
        return Storage::disk($this->disk)->temporaryUrl($path, $expiration);
    }

    public function download(string $path)
    {
        return Storage::disk($this->disk)->download($path);
    }

    public function exists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

    public function delete(string $path): bool
    {
        return Storage::disk($this->disk)->delete($path);
    }

    public function move(string $from, string $to): bool
    {
        return Storage::disk($this->disk)->move($from, $to);
    }

    public function copy(string $from, string $to): bool
    {
        return Storage::disk($this->disk)->copy($from, $to);
    }

    public function files(string $directory): array
    {
        return Storage::disk($this->disk)->files($directory);
    }

    public function directories(string $directory): array
    {
        return Storage::disk($this->disk)->directories($directory);
    }
}

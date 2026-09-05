<?php

namespace App\Traits;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        // Génère automatiquement un UUID lors de la création d'un modèle
        static::creating(function ($model) {

            // Vérifie si la colonne 'uuid' existe dans la table du modèle
            if (!$model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'uuid')) {
                return;
            }

            // Si l'UUID est vide, génère un nouvel UUID trié chronologiquement
            if (empty($model->uuid)) {

                // Génère un UUID trié chronologiquement basé sur le timestamp en microsecondes et une partie aléatoire
                $uuid = static::generateTimeOrderedUuid();

                // Vérifie si l'UUID généré existe déjà dans la base de données pour éviter les collisions
                while (static::where('uuid', $uuid)->exists()) {
                    // Si l'UUID existe déjà, génère un nouvel UUID
                    $uuid = static::generateTimeOrderedUuid();
                }

                // Génère un UUID trié chronologiquement basé sur le timestamp en microsecondes et une partie aléatoire
                $model->uuid = $uuid;
            }
        });
    }

    /**
     * Génère un identifiant trié chronologiquement, sur base des microsecondes.
     *
     * Structure :
     *  - 16 caractères hexa : timestamp en microsecondes (poids fort → domine le tri)
     *  - 16 caractères hexa : aléatoire (évite les collisions en cas d'appels simultanés)
     *
     * orderBy('uuid', 'asc')  === orderBy('created_at', 'asc')
     * orderBy('uuid', 'desc') === orderBy('created_at', 'desc')
     */
    protected static function generateTimeOrderedUuid(): string
    {
        // Microsecondes écoulées depuis l'epoch Unix
        $microtime = (int) (microtime(true) * 1_000_000);

        // Timestamp en DÉCIMAL (chiffres 0-9 uniquement), rembourré à 16 chiffres
        // Actuellement ~16 chiffres (ex: 1786805948053622) → reste valable jusqu'en ~2287
        $timePart = str_pad((string) $microtime, 16, '0', STR_PAD_LEFT);

        // Partie aléatoire (peut rester en hexa, ou en décimal aussi si besoin — voir plus bas)
        $randomPart = bin2hex(random_bytes(8)); // 16 caractères hexa

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($timePart, 0, 8),
            substr($timePart, 8, 4),
            substr($timePart, 12, 4),
            substr($randomPart, 0, 4),
            substr($randomPart, 4, 12)
        );
    }
}

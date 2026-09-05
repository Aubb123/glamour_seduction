<?php

namespace App\Traits;

trait HasDatetime
{
    /**
     * Boot du trait — branché automatiquement sur les events Eloquent
     */
    public static function bootHasDatetime(): void
    {
        // Lors d'un CREATE
        static::creating(function ($model) {
            $now = self::getDateTime();
            $model->created_at = $now;
            $model->updated_at = $now;
        });

        // Lors d'un UPDATE
        static::updating(function ($model) {
            $model->updated_at = self::getDateTime();
        });
    }

    /**
     * Retourne la date et l'heure actuelle formatée
     */
    public static function getDateTime(): string
    {
        return getDateTime();
    }
}

<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Clé étrangère utilisée pour les traductions
    protected string $translationForeignKey = 'setting_id';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'is_public',
        'is_translatable',
        'is_editable',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'is_public' => 'boolean',
        'is_translatable' => 'boolean',
        'is_editable' => 'boolean',
    ];

    /**
     * Récupère la valeur du setting en fonction de la langue.
     */
    public function getValue(?string $locale = null): mixed
    {
        if (! $this->is_translatable) {
            return $this->castValue($this->value);
        }

        $translation = $this->getTranslationFor($locale);
        $translationValue = $translation?->value;

        // La valeur traduite est prioritaire si elle est définie (non null, non vide).
        // Sinon, on retombe sur la valeur par défaut du setting (fallback).
        if ($translationValue !== null && $translationValue !== '') {
            return $this->castValue($translationValue);
        }

        return $this->castValue($this->value);
    }

    /**
     * Convertit la valeur brute selon le type du setting.
     */
    private function castValue($value): mixed
    {
        return match ($this->type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Scope pour filtrer les settings par groupe.
     */
    public function scopeGroup($query, string $group): mixed
    {
        return $query->where('group', $group);
    }

    /**
     * Accesseur : clé de formulaire unique basée sur l'UUID.
     */
    public function getFormKeyAttribute(): string
    {
        return 'settings['.$this->uuid.']';
    }

    /**
     * Récupère la traduction du setting pour un lang_id donné.
     */
    public function getTranslationForLang(int $langId): ?SettingTranslation
    {
        return $this->translations->firstWhere('lang_id', $langId);
    }

    /**
     * Retourne la clé de formulaire pour une traduction dans une langue donnée.
     */
    public function getTranslationFormKey(int $langId): string
    {
        return $this->form_key.'[translations]['.$langId.']';
    }

    /**
     * Scope pour récupérer uniquement les settings publics.
     */
    public function scopePublic($query): mixed
    {
        return $query->where('is_public', true);
    }
}

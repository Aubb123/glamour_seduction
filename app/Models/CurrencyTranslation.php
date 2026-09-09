<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrencyTranslation extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table de traduction des devises
    protected $table = 'currency_translations';

    // Les traductions gèrent les timestamps
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'currency_id',
        'lang_id',
        'name',
        'slug',
        'description',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {currencyTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers la devise parente.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}
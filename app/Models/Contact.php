<?php

namespace App\Models;

use App\Models\User;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Les contacts ne gèrent pas de traductions séparées
    protected bool $hasTranslations = false;

    // Table associée aux demandes de contact
    protected $table = 'contacts';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'user_id',
        'fullname',
        'email',
        'phone',
        'subject',
        'message',
        'is_read',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'is_read' => 'boolean',
    ];

    // Clé de route utilisée pour le binding implicite ({contact})
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers l'utilisateur qui a soumis le contact (optionnel).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

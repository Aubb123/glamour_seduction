<?php

namespace App\Models;

use App\Models\Lang;
use App\Models\Role;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleTranslation extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {roleTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Table de traduction des rôles
    protected $table = 'role_translations';

    // Les traductions ne gèrent pas created_at / updated_at automatiquement
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'role_id',
        'lang_id',
        'label',
        'description',
    ];

    // Champ source utilisé par le trait HasSlug pour générer le slug
    // ('label' car la table role_translations n'a pas de colonne 'name')
    protected static function getSlugSourceField(): string
    {
        return 'label';
    }

    // Champs réels à surveiller pour la régénération du slug lorsque le libellé change
    protected static function getSlugWatchedFields(): array
    {
        return ['label'];
    }

    /**
     * Relation vers le rôle parent.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}

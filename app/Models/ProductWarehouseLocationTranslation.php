<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductWarehouseLocationTranslation extends Model
{
    // Traits pour les comportements communs (UUID, timestamps, soft deletes, etc.)
    use HasModelBehaviors;

    // Table de traduction des emplacements d'entrepôt
    protected $table = 'product_warehouse_location_translations';

    // Gestion automatique de created_at / updated_at (la table possède ces colonnes via $table->timestamps())
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    // NB : pas de slug — le nom est unique par entrepôt et par langue (règle applicative).
    protected $fillable = [
        'location_id',
        'lang_id',
        'name',
        'description',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productWarehouseLocationTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers l'emplacement parent.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(ProductWarehouseLocation::class, 'location_id');
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}

<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductWarehouseTranslation extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table de traduction des entrepôts
    protected $table = 'product_warehouse_translations';

    // Gestion automatique de created_at / updated_at (la table possède ces colonnes via $table->timestamps())
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'warehouse_id',
        'lang_id',
        'name',
        'description',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productWarehouseTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Source utilisée pour générer automatiquement le slug.
     */
    protected static function getSlugSourceField(): string
    {
        return 'name';
    }

    /**
     * Champs surveillés pour régénérer le slug lors d'une mise à jour.
     */
    protected static function getSlugWatchedFields(): array
    {
        return ['name'];
    }

    /**
     * Relation vers l'entrepôt parent.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ProductWarehouse::class, 'warehouse_id');
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}

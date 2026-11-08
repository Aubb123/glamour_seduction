<?php

namespace App\Models;

use App\Models\Lang;
use App\Models\ProductWarehouse;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductWarehouseTranslation extends Model
{
    // Comportements partagés entre modèles (ex. UUID, slug, timestamps personnalisés)
    use HasModelBehaviors;

    // Table de traduction des entrepôts
    protected $table = 'product_warehouse_translations';

    // Gestion automatique de created_at / updated_at (la table possède ces colonnes via $table->timestamps())
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'warehouse_id', // clé étrangère vers product_warehouses
        'lang_id',      // clé étrangère vers langs
        'name',         // nom de l'entrepôt traduit
        'slug',         // slug SEO de l'entrepôt traduit (généré automatiquement)
        'address',      // adresse traduite de l'entrepôt
        'city',         // ville traduite de l'entrepôt
        'phone',        // téléphone traduit de l'entrepôt
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite ({productWarehouseTranslation}).
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
     * Retourne l'entrepôt parent de cette traduction.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ProductWarehouse::class, 'warehouse_id');
    }

    /**
     * Retourne la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}

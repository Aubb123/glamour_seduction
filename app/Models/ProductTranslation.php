<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTranslation extends Model
{
    // Comportements partagés entre modèles (ex. UUID, slug, timestamps personnalisés)
    use HasModelBehaviors;

    // Table de traduction des produits
    protected $table = 'product_translations';

    // Gestion automatique de created_at / updated_at (la table possède ces colonnes via $table->timestamps())
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'product_id',        // clé étrangère vers products
        'lang_id',           // clé étrangère vers langs
        'name',              // nom du produit traduit
        'slug',              // slug SEO du produit traduit
        'short_description', // description courte traduite (max 500 caractères)
        'description',       // description longue traduite
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite ({productTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Retourne le produit parent de cette traduction.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Retourne la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}

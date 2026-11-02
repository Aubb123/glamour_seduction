<?php

namespace App\Models;

use App\Models\Lang;
use App\Models\ProductCategory;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCategoryTranslation extends Model
{
    // Comportements partagés entre modèles (ex. UUID, slug, timestamps personnalisés)
    use HasModelBehaviors;

    // Table de traduction des catégories de produits
    protected $table = 'product_category_translations';

    // Gestion automatique de created_at / updated_at (la table possède ces colonnes via $table->timestamps())
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'category_id',   // clé étrangère vers product_categories
        'lang_id',       // clé étrangère vers langs
        'name',          // nom de la catégorie traduit
        'slug',          // slug SEO de la catégorie traduit
        'description',   // description traduite de la catégorie
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite ({productCategoryTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Retourne la catégorie parente de cette traduction.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Retourne la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}

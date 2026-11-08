<?php

namespace App\Models;

use App\Models\Lang;
use App\Models\ProductAttribute;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeTranslation extends Model
{
    // Comportements partagés entre modèles (ex. UUID, slug, timestamps personnalisés)
    use HasModelBehaviors;

    // Table de traduction des attributs de produits
    protected $table = 'product_attribute_translations';

    // Gestion automatique de created_at / updated_at (la table possède ces colonnes via $table->timestamps())
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'attribute_id',  // clé étrangère vers product_attributes
        'lang_id',       // clé étrangère vers langs
        'name',          // nom de l'attribut traduit (ex : Couleur, Taille)
        'slug',          // slug SEO de l'attribut traduit
        'description',   // description traduite de l'attribut
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite ({productAttributeTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Retourne l'attribut parent de cette traduction.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    /**
     * Retourne la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}

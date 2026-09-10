<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValueTranslation extends Model
{
    // Comportements partagés entre modèles (ex. UUID, slug, timestamps personnalisés)
    use HasModelBehaviors;

    // Table de traduction des valeurs d'attributs de produits
    protected $table = 'product_attribute_value_translations';

    // Gestion automatique de created_at / updated_at (la table possède ces colonnes via $table->timestamps())
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'attribute_value_id', // clé étrangère vers product_attribute_values
        'lang_id',            // clé étrangère vers langs
        'label',              // libellé affiché de la valeur traduit (ex : Rouge, XL)
        'slug',               // slug SEO de la valeur traduite
    ];

    /**
     * Champ source du slug : les traductions de valeur utilisent `label`
     * (et non `name`) comme champ source pour la génération automatique
     * du slug par le trait HasSlug.
     */
    protected static function getSlugSourceField(): string
    {
        return 'label';
    }

    /**
     * Clé de liaison utilisée par le model binding implicite ({productAttributeValueTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Retourne la valeur d'attribut parente de cette traduction.
     */
    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(ProductAttributeValue::class, 'attribute_value_id');
    }

    /**
     * Retourne la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}
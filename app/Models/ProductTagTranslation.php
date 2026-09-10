<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTagTranslation extends Model
{
    // Comportements partagés entre modèles (ex. UUID, slug, timestamps personnalisés)
    use HasModelBehaviors;

    // Table de traduction des tags de produits
    protected $table = 'product_tag_translations';

    // Gestion automatique de created_at / updated_at (la table possède ces colonnes via $table->timestamps())
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'tag_id',        // clé étrangère vers product_tags
        'lang_id',       // clé étrangère vers langs
        'name',          // nom du tag traduit
        'slug',          // slug SEO du tag traduit
        'description',   // description traduite du tag
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite ({productTagTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Retourne le tag parent de cette traduction.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(ProductTag::class, 'tag_id');
    }

    /**
     * Retourne la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}

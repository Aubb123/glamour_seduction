<?php

namespace App\Models;

use App\Models\Lang;
use App\Models\Shop;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopTranslation extends Model
{
    // Comportements partagés entre modèles (ex. UUID, slug, timestamps personnalisés)
    use HasModelBehaviors;

    // Table de traduction des boutiques
    protected $table = 'shop_translations';

    // Gestion automatique de created_at / updated_at (la table possède ces colonnes via $table->timestamps())
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'shop_id',      // clé étrangère vers shops
        'lang_id',      // clé étrangère vers langs
        'name',         // nom de la boutique traduit
        'slug',         // slug SEO de la boutique traduit
        'description',  // description traduite de la boutique
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite ({shopTranslation}).
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
     * Retourne la boutique parente de cette traduction.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * Retourne la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}

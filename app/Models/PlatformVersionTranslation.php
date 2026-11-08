<?php

namespace App\Models;

use App\Models\Lang;
use App\Models\PlatformVersion;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformVersionTranslation extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table de traduction des versions de plateforme
    protected $table = 'platform_version_translations';

    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'platform_version_id',
        'lang_id',
        'name',
        'slug',
        'description',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {platformVersionTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers la version de plateforme parent.
     */
    public function platformVersion(): BelongsTo
    {
        return $this->belongsTo(PlatformVersion::class);
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}

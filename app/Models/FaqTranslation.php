<?php

namespace App\Models;

use App\Models\Faq;
use App\Models\Lang;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaqTranslation extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table de traduction des FAQ
    protected $table = 'faq_translations';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'faq_id',
        'lang_id',
        'question',
        'answer',
    ];

    // Gestion automatique de created_at / updated_at (la table possède ces colonnes via $table->timestamps())
    public $timestamps = true;

    /**
     * Retourne le champ source utilisé par HasSlug pour générer le slug.
     */
    protected static function getSlugSourceField(): string
    {
        return 'question';
    }

    /**
     * Champs à surveiller pour régénérer le slug lorsque la question change.
     */
    protected static function getSlugWatchedFields(): array
    {
        return ['question'];
    }

    /**
     * Relation vers la FAQ parent.
     */
    public function faq(): BelongsTo
    {
        return $this->belongsTo(Faq::class);
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}

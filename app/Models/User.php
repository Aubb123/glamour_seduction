<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Users\UserStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

// Attributs de masse assignables
#[Fillable(['country_id', 'first_name', 'last_name', 'email', 'gender', 'phone', 'password', 'status'])]

// Attributs à cacher lors de la sérialisation
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors, HasRoles, Notifiable;

    // Les utilisateurs ne gèrent pas de traductions séparées
    protected bool $hasTranslations = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    // Clé de route utilisée pour le binding implicite ({user} dans les URLs)
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Champ source utilisé par le trait HasSlug pour générer le slug
    protected static function getSlugSourceField(): string
    {
        return 'first_last';
    }

    // Champs réels à surveiller pour la régénération du slug
    // (first_last est un accessor virtuel, non trackable directement par isDirty())
    protected static function getSlugWatchedFields(): array
    {
        return ['first_name', 'last_name'];
    }

    // Accessor combinant first_name + last_name pour être utilisé par le trait HasSlug
    public function getFirstLastAttribute(): string
    {
        return trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
    }

    /**
     * Scope pour récupérer uniquement les utilisateurs actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', UserStatus::Active);
    }

    /**
     * Scope pour récupérer uniquement les utilisateurs NON actifs
     * (suspendus ou bannis).
     */
    public function scopeNotActive($query): Builder
    {
        return $query->whereIn('status', [UserStatus::Suspended, UserStatus::Banned]);
    }

    // ─────────────────────────────────────────────
    // RELATIONS
    // ─────────────────────────────────────────────

    /**
     * Relation vers le pays de l'utilisateur.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(GeoCountry::class, 'country_id');
    }

    /**
     * Relation : un utilisateur a plusieurs galeries.
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'user_id');
    }

    /**
     * Relation : un utilisateur a plusieurs abonnements.
     */
    public function subscribes(): HasMany
    {
        return $this->hasMany(Subscribe::class, 'user_id');
    }

    /**
     * Relation : un utilisateur a plusieurs audits.
     */
    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    /**
     * Relation : un utilisateur a plusieurs messages de contact.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'user_id');
    }

    /**
     * Relation : un utilisateur a plusieurs FAQ.
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'user_id');
    }

    /**
     * Relation : un utilisateur a plusieurs articles de blog.
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'user_id');
    }

    /**
     * Relation : un utilisateur a plusieurs commentaires de blog.
     */
    public function blogPostComments(): HasMany
    {
        return $this->hasMany(BlogPostComment::class, 'user_id');
    }

    /**
     * Relation : un utilisateur a plusieurs réponses de commentaires de blog.
     */
    public function blogPostCommentReplies(): HasMany
    {
        return $this->hasMany(BlogPostCommentReply::class, 'user_id');
    }
}

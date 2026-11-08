<?php

namespace App\Models;

use App\Enums\Blogs\Categories\BlogCategoryStatus;
use App\Models\BlogPost;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    protected $table = 'blog_categories'; // Précise le nom de la table en base de donnée si différent du nom du modèle

    // 👇 CLÉ ÉTRANGÈRE POUR LES TRADUCTIONS — Obligatoire car la colonne s'appelle `category_id` et non `blog_category_id`
    protected string $translationForeignKey = 'category_id';

    // Relations enfants dont la présence bloque la suppression de cette catégorie (delete / force_delete) lors d'une supression temporaire (BackendHelper::destroy_function) ou définitive (BackendHelper::force_destroy_function)
    protected array $dependentRelations = ['posts'];

    // $fillable définit les champs qui peuvent être remplis en masse
    protected $fillable = [
        'sort_order',
        'status',
    ];

    // $casts définit les types de données pour les champs
    protected $casts = [
        'status' => BlogCategoryStatus::class,
    ];

    // Clé de route utilisée pour le binding implicite ({blogCategory} dans les URLs)
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Une catégorie a plusieurs posts
    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }

    // Scope catégories actives
    public function scopeActive($query): Builder
    {
        return $query->where('status', BlogCategoryStatus::Active);
    }
}

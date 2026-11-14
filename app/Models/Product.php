<?php

namespace App\Models;

use App\Enums\Products\Products\ProductStatus;
use App\Models\Currency;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductTranslation;
use App\Models\ProductVariant;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasModelBehaviors;

    protected $table = 'products';
    protected string $translationForeignKey = 'product_id';
    protected string $translationModel = ProductTranslation::class;
    protected array $dependentRelations = ['variants'];

    protected $fillable = [
        'category_id', 'brand_id', 'currency_id', 'status', 'is_featured', 'views_count', 'weight', 'base_price', 'compare_at_price',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'weight' => 'decimal:2',
        'base_price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class, 'product_tag', 'product_id', 'tag_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function scopePublished($query): Builder
    {
        return $query->where('status', ProductStatus::Published);
    }

    public function scopeFeatured($query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeDraft($query): Builder
    {
        return $query->where('status', ProductStatus::Draft);
    }

    public function scopeArchived($query): Builder
    {
        return $query->where('status', ProductStatus::Archived);
    }

    public function isPublished(): bool
    {
        return $this->status === ProductStatus::Published;
    }
}

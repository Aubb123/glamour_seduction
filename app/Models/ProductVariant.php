<?php

namespace App\Models;

use App\Enums\Products\Variants\ProductVariantStatus;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductStockMovement;
use App\Models\ProductWarehouseStock;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ProductVariant extends Model
{
    use HasModelBehaviors;

    protected $table = 'product_variants';
    protected bool $hasTranslations = false;
    protected array $dependentRelations = ['stocks', 'movements'];

    protected $fillable = ['product_id', 'sku', 'barcode', 'price', 'status', 'is_default'];

    protected $casts = [
        'status' => ProductVariantStatus::class,
        'is_default' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttributeValue::class, 'product_variant_attribute_value', 'product_variant_id', 'attribute_value_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductWarehouseStock::class, 'product_variant_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(ProductStockMovement::class, 'product_variant_id');
    }

    /**
     * Images to display for this variant. When the variant has no own images,
     * the product gallery is returned as a fallback for storefront selectors.
     */
    public function displayImages(): Collection
    {
        $variantImages = $this->cloudFiles->where('file_type', 'image')
            ->sortBy(fn ($file) => $file->metadata['page'] ?? 0)
            ->values();

        if ($variantImages->isNotEmpty()) {
            return $variantImages;
        }

        return $this->product?->cloudFiles
            ?->where('file_type', 'image')
            ->sortBy(fn ($file) => $file->metadata['page'] ?? 0)
            ->values() ?? collect();
    }

    public function effectivePrice(): string
    {
        return $this->price ?? $this->product?->base_price ?? '0.00';
    }

    public function scopeActive($query): Builder
    {
        return $query->where('status', ProductVariantStatus::Active);
    }
}

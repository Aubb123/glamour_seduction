<?php

namespace App\Models;

use App\Enums\Products\Variants\ProductVariantStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Variante d'un produit (ex. « T-shirt rouge, taille M »).
 *
 * Contraintes d'unicité (par conception) :
 * - le SKU est unique GLOBALEMENT (toutes variantes confondues), pas par produit ;
 * - le code-barres est unique lorsqu'il est renseigné (`NULL` reste autorisé
 *   plusieurs fois, comportement standard de MySQL/SQLite sur index unique).
 * - une combinaison de valeurs d'attributs ne peut exister qu'une seule fois
 *   par produit (voir `attributeCombinationExists()`).
 */
class ProductVariant extends Model
{
    use HasModelBehaviors;

    protected $table = 'product_variants';

    protected bool $hasTranslations = false;

    protected array $dependentRelations = [];

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

    /**
     * Relation vers les soldes de stock globaux de cette variante (par entrepôt).
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ProductWarehouseStock::class, 'product_variant_id');
    }

    /**
     * Relation vers les soldes de stock détaillés de cette variante (par emplacement).
     */
    public function locationStocks(): HasMany
    {
        return $this->hasMany(ProductWarehouseLocationStock::class, 'product_variant_id');
    }

    /**
     * Indique si une combinaison de valeurs d'attributs est déjà utilisée
     * par une autre variante (non supprimée) du même produit.
     *
     * La comparaison se fait sur des ensembles : l'ordre et les doublons sont
     * ignorés. Un ensemble vide (variante sans options) n'est jamais considéré
     * comme un doublon : plusieurs variantes « sans options » restent autorisées.
     *
     * @param  array<int, int|string>  $attributeValueIds
     */
    public static function attributeCombinationExists(int $productId, array $attributeValueIds, ?int $exceptId = null): bool
    {
        $candidate = array_values(array_unique(array_filter(array_map('intval', $attributeValueIds))));
        sort($candidate);

        if ($candidate === []) {
            return false;
        }

        // Charge toutes les variantes du produit en une requête (évite le N+1).
        $variants = static::query()
            ->where('product_id', $productId)
            ->when($exceptId !== null, fn (Builder $query) => $query->whereKeyNot($exceptId))
            ->with('attributeValues:id')
            ->get();

        foreach ($variants as $variant) {
            $existing = $variant->attributeValues
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
            sort($existing);

            if ($existing === $candidate) {
                return true;
            }
        }

        return false;
    }

    /** Images shown for this variant, falling back to the parent product gallery. */
    public function displayImages(): Collection
    {
        $ownImages = $this->cloudFiles->where('file_type', 'image')
            ->sortBy(fn ($file) => $file->metadata['page'] ?? 0)
            ->values();

        if ($ownImages->isNotEmpty()) {
            return $ownImages;
        }

        $product = $this->product;
        if (! $product) {
            return collect();
        }

        return $product->cloudFiles->where('file_type', 'image')
            ->sortBy(fn ($file) => $file->metadata['page'] ?? 0)
            ->values();
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

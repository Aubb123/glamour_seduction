<form id="{{ isset($variant) ? 'edit-variant-form' : 'create-variant-form' }}" action="{{ isset($variant) ? route('backend.product-variants.update', ['productVariant' => $variant->uuid]) : route('backend.product-variants.store', ['product' => $product->uuid]) }}" method="POST">
    @csrf
    @if (isset($variant)) @method('PUT') @endif

    <div class="d-flex align-items-center mb-4 gap-3">
        <div class="step-item active" data-step="1">
            <span class="step-badge">1</span>
            <span class="step-label">Informations générales</span>
        </div>
        <div class="step-divider"></div>
        <div class="step-item" data-step="2">
            <span class="step-badge">2</span>
            <span class="step-label">Prix &amp; Options</span>
        </div>
        <div class="step-divider"></div>
        <div class="step-item" data-step="3">
            <span class="step-badge">3</span>
            <span class="step-label">Valeurs d'attributs</span>
        </div>
    </div>

    {{-- Étape 1 : informations générales --}}
    <div class="step-content" data-step-content="1">

        {{-- Produit parent : affiché en lecture seule — jamais sélectionnable ni modifiable --}}
        <div class="card border mb-3">
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col-md-4 text-muted fw-semibold">Produit parent</div>
                    <div class="col-md-8">
                        <span class="badge badge-soft-primary">
                            {{ (isset($variant) ? $variant->product?->active_translation?->name : $product->active_translation?->name) ?? '#' . (isset($variant) ? $variant->product_id : $product->id) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="sku" class="form-label">Référence SKU <span class="text-danger">*</span></label>
                <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" placeholder="ex : MAGIC-ROUGE-S" value="{{ old('sku', $variant->sku ?? '') }}" required />
                @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="barcode" class="form-label">Code-barres</label>
                <input type="text" name="barcode" id="barcode" class="form-control @error('barcode') is-invalid @enderror" placeholder="ex : EAN-13" value="{{ old('barcode', $variant->barcode ?? '') }}" />
                @error('barcode') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

    </div>

    {{-- Étape 2 : prix & options --}}
    <div class="step-content d-none" data-step-content="2">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Prix</label>
                <input type="number" step="0.01" min="0.01" name="price" id="price" class="form-control @error('price') is-invalid @enderror" placeholder="0.00" value="{{ old('price', $variant->price ?? null) }}" />
                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">Laissez vide pour hériter du prix du produit.</small>
            </div>

            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Statut</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="active" {{ old('status', $variant->status->value ?? 'active') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ old('status', $variant->status->value ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $variant->is_default ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_default">Variante par défaut</label>
                </div>
                <small class="text-muted">Une seule variante par défaut par produit.</small>
            </div>
        </div>

    </div>

    {{-- Étape 3 : valeurs d'attributs --}}
    <div class="step-content d-none" data-step-content="3">

        <h6 class="fw-bold mb-3">Valeurs d'attributs</h6>
        <p class="text-muted small">Combinaison de valeurs (ex : Couleur = Rouge, Taille = S).</p>

        @php
            $selectedValueIds = old('attribute_value_ids', isset($variant) ? $variant->attributeValues->pluck('id')->all() : []);
        @endphp

        <div class="row">
            @foreach ($attributes as $attribute)
                <div class="col-md-12 mb-3">
                    <label class="form-label d-block fw-semibold">{{ $attribute->active_translation?->name ?? ('#'.$attribute->id) }}</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($attribute->values as $value)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="attribute_value_ids[]" id="attr-value-{{ $value->id }}" value="{{ $value->id }}"
                                    @if (in_array($value->id, $selectedValueIds)) checked @endif />
                                <label class="form-check-label" for="attr-value-{{ $value->id }}">
                                    {{ $value->active_translation?->label ?? $value->value }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <hr class="my-4">

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ (isset($variant) ? route('backend.products.show', ['product' => $variant->product?->uuid, 'slug' => $variant->product?->active_translation?->slug ?? 'null']) : route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null'])) }}" class="btn btn-secondary">
            <i class="bx bx-x me-1"></i> Annuler
        </a>
        <button type="submit" class="btn btn-success">
            <i class="bx bx-save me-1"></i> {{ isset($variant) ? 'Mettre à jour' : 'Enregistrer' }}
        </button>
    </div>
</form>

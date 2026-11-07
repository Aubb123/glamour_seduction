@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Créer une variante de produit') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Créer une variante de produit')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Créer une variante') }} — {{ $product->active_translation?->name ?? ('#'.$product->id) }}</h5>
                    <a href="{{ route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']) }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour au produit') }}
                    </a>
                </div>

                <div class="card-body">

                    {{-- Stepper Nav --}}
                    <div class="d-flex align-items-center mb-4 gap-3">
                        <div class="step-item active" data-step="1">
                            <span class="step-badge">1</span>
                            <span class="step-label">{{ __('Informations générales') }}</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-item" data-step="2">
                            <span class="step-badge">2</span>
                            <span class="step-label">{{ __('Prix & Options') }}</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-item" data-step="3">
                            <span class="step-badge">3</span>
                            <span class="step-label">{{ __("Valeurs d'attributs") }}</span>
                        </div>
                    </div>

                    <form id="create-variant-form" action="{{ route('backend.product-variants.store', ['product' => $product->uuid]) }}" method="POST">
                        @csrf

                        {{-- Étape 1 : informations générales --}}
                        <div class="step-content" data-step-content="1">

                            {{-- Produit parent : affiché en lecture seule — jamais sélectionnable ni modifiable.
                                 La variante appartient à UN produit, résolu depuis l'URL (?product=uuid). --}}
                            <div class="card border mb-3">
                                <div class="card-body py-2">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 text-muted fw-semibold">{{ __('Produit parent') }}</div>
                                        <div class="col-md-8">
                                            <span class="badge badge-soft-primary">
                                                {{ $product->active_translation?->name ?? ('#'.$product->id) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sku" class="form-label">{{ __('Référence SKU') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" placeholder="ex : MAGIC-ROUGE-S" value="{{ old('sku') }}" required />
                                    @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="barcode" class="form-label">{{ __('Code-barres') }}</label>
                                    <input type="text" name="barcode" id="barcode" class="form-control @error('barcode') is-invalid @enderror" placeholder="ex : EAN-13" value="{{ old('barcode') }}" />
                                    @error('barcode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                        </div>

                        {{-- Étape 2 : prix & options --}}
                        <div class="step-content d-none" data-step-content="2">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">{{ __('Prix') }}</label>
                                    <input type="number" step="0.01" min="0.01" name="price" id="price" class="form-control @error('price') is-invalid @enderror" placeholder="0.00" value="{{ old('price') }}" />
                                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('Laissez vide pour hériter du prix du produit.') }}</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ __('Statut') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        @foreach (\App\Enums\Products\Variants\ProductVariantStatus::cases() as $status)
                                            <option value="{{ $status->value }}" {{ old('status', 'active') === $status->value ? 'selected' : '' }}>
                                                {{ $status->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_default">{{ __('Variante par défaut') }}</label>
                                    </div>
                                    <small class="text-muted">{{ __('Une seule variante par défaut par produit.') }}</small>
                                </div>
                            </div>

                        </div>

                        {{-- Étape 3 : valeurs d'attributs --}}
                        <div class="step-content d-none" data-step-content="3">

                            <h6 class="fw-bold mb-3">{{ __("Valeurs d'attributs") }}</h6>
                            <p class="text-muted small">{{ __("Combinaison de valeurs (ex : Couleur = Rouge, Taille = S).") }}</p>

                            @php($selectedValueIds = old('attribute_value_ids', []))

                            <div class="row">
                                @forelse ($attributes as $attribute)
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label d-block fw-semibold">{{ $attribute->active_translation?->name ?? ('#'.$attribute->id) }}</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($attribute->values as $value)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="attribute_value_ids[]" id="attr-value-{{ $value->id }}" value="{{ $value->id }}"
                                                        @if (in_array($value->id, $selectedValueIds)) checked @endif />
                                                    <label class="form-check-label" for="attr-value-{{ $value->id }}">
                                                        @include('v1.backend.pages.products.variants.partials._color-swatch', ['hex' => $value->hex_color])
                                                        {{ $value->active_translation?->label ?? $value->value }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-warning mb-0">{{ __('Aucun attribut trouvé.') }}</div>
                                    </div>
                                @endforelse
                            </div>

                        </div>

                        {{-- Boutons navigation --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary d-none" id="btn-prev">
                                <i class="bx bx-arrow-back me-1"></i> {{ __('Précédent') }}
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <a href="{{ route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']) }}" class="btn btn-light">
                                    <i class="bx bx-x me-1"></i> {{ __('Annuler') }}
                                </a>
                                <button type="button" class="btn btn-primary" id="btn-next">
                                    {{ __('Suivant') }} <i class="bx bx-right-arrow-alt ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-success d-none" id="btn-submit">
                                    <i class="bx bx-save me-1"></i> {{ __('Enregistrer') }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @include('v1.backend.layouts.partials._stepper-script', ['formId' => 'create-variant-form'])
@endpush

@push('styles')
<style>
    .step-item { display: flex; align-items: center; gap: 8px; opacity: 0.4; }
    .step-item.active, .step-item.done { opacity: 1; }
    .step-badge {
        width: 30px; height: 30px; border-radius: 50%; background: #e9ecef;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 13px;
    }
    .step-item.active .step-badge { background: #0c0c0c; color: #fff; }
    .step-item.done .step-badge   { background: green; color: #fff; }
    .step-divider { flex: 1; height: 2px; background: #e9ecef; min-width: 40px; }
    .step-label { font-size: 13px; font-weight: 500; white-space: nowrap; }
</style>
@endpush

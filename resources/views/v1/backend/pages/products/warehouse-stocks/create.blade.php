@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Créer un stock') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Créer un stock')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Créer un stock') }}</h5>
                    <a href="{{ route('backend.product-warehouse-stocks.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">

                    @if ($warehouses->isEmpty())
                        <div class="alert alert-warning">
                            {{ __('Aucun entrepôt en mode « warehouse » n\'est disponible. Configurez le mode de suivi du stock d\'un entrepôt avant de créer un stock global.') }}
                        </div>
                    @else

                        <form id="create-stock-form" action="{{ route('backend.product-warehouse-stocks.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="warehouse_id" class="form-label">{{ __('Entrepôt') }} <span class="text-danger">*</span></label>
                                    <select name="warehouse_id" id="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                        <option value="">{{ __('Sélectionner un entrepôt') }}</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->active_translation?->name ?? $warehouse->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('Seuls les entrepôts en mode « warehouse » (suivi global) sont listés.') }}</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="product_variant_id" class="form-label">{{ __('Variante') }} <span class="text-danger">*</span></label>
                                    <select name="product_variant_id" id="product_variant_id" class="form-select @error('product_variant_id') is-invalid @enderror" required>
                                        <option value="">{{ __('Sélectionner une variante') }}</option>
                                        @foreach ($variants as $variant)
                                            <option value="{{ $variant->id }}" {{ old('product_variant_id') == $variant->id ? 'selected' : '' }}>
                                                {{ $variant->sku }} — {{ $variant->product?->active_translation?->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_variant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="quantity" class="form-label">{{ __('Quantité') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="1" min="0" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" placeholder="0" value="{{ old('quantity', '0') }}" required />
                                    @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('La quantité ne peut pas être négative.') }}</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="bx bx-save me-1"></i> {{ __('Enregistrer') }}
                                </button>
                            </div>

                        </form>

                    @endif

                </div>
            </div>
        </div>
    </div>

@endsection
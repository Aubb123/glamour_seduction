@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Modifier le stock') }} — <code>{{ $stock->variant?->sku ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Modifier le stock')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('Modifier') }} — <code>{{ $stock->variant?->sku ?? '' }}</code>
                    </h5>
                    <a href="{{ route('backend.product-warehouse-stocks.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <div class="d-flex align-items-center mb-4 gap-3">
                        <div class="step-item" data-step="1">
                            <span class="step-badge">1</span>
                            <span class="step-label">{{ __('Informations générales') }}</span>
                        </div>
                    </div>

                    <form id="edit-stock-form" action="{{ route('backend.product-warehouse-stocks.update', ['productWarehouseStock' => $stock->uuid]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="step-content" data-step-content="1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="warehouse_id" class="form-label">{{ __('Entrepôt') }} <span class="text-danger">*</span></label>
                                    <select name="warehouse_id" id="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                        <option value="">— {{ __('Choisir un entrepôt') }} —</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" @selected($stock->warehouse_id == $warehouse->id or old('warehouse_id') == $warehouse->id)>
                                                {{ $warehouse->name }} ({{ $warehouse->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="product_variant_id" class="form-label">{{ __('Variante') }} <span class="text-danger">*</span></label>
                                    <select name="product_variant_id" id="product_variant_id" class="form-select @error('product_variant_id') is-invalid @enderror" required>
                                        <option value="">— {{ __('Choisir une variante') }} —</option>
                                        @foreach ($variants as $variant)
                                            <option value="{{ $variant->id }}" @selected($stock->product_variant_id == $variant->id or old('product_variant_id') == $variant->id)>
                                                {{ $variant->product?->active_translation?->name ?? '#' . $variant->product_id }} — {{ $variant->sku }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_variant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="quantity" class="form-label">{{ __('Quantité') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" id="quantity" min="0" step="1" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $stock->quantity) }}" required />
                                    @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="reserved_quantity" class="form-label">{{ __('Quantité réservée') }}</label>
                                    <input type="number" name="reserved_quantity" id="reserved_quantity" min="0" step="1" class="form-control @error('reserved_quantity') is-invalid @enderror" value="{{ old('reserved_quantity', $stock->reserved_quantity) }}" />
                                    @error('reserved_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="low_stock_threshold" class="form-label">{{ __("Seuil d'alerte") }} <span class="text-danger">*</span></label>
                                    <input type="number" name="low_stock_threshold" id="low_stock_threshold" min="0" step="1" class="form-control @error('low_stock_threshold') is-invalid @enderror" value="{{ old('low_stock_threshold', $stock->low_stock_threshold) }}" required />
                                    @error('low_stock_threshold') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <div class="ms-auto d-flex gap-2">
                                <a href="{{ route('backend.product-warehouse-stocks.index') }}" class="btn btn-light">
                                    <i class="bx bx-x me-1"></i> {{ __('Annuler') }}
                                </a>
                                <button type="submit" class="btn btn-success">
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
    @include('v1.backend.layouts.partials._stepper-script', ['formId' => 'edit-stock-form'])
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

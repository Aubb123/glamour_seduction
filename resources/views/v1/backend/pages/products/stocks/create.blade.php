@extends('v1.backend.layouts.app')

@section('title') Créer un stock @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Créer un stock'])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Créer un stock</h5>
                    <a href="{{ route('backend.product-warehouse-stocks.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    <form id="create-stock-form" action="{{ route('backend.product-warehouse-stocks.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="warehouse_id" class="form-label">Entrepôt <span class="text-danger">*</span></label>
                                <select name="warehouse_id" id="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                    <option value="">— Choisir un entrepôt —</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>
                                            {{ $warehouse->name }} ({{ $warehouse->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="product_variant_id" class="form-label">Variante <span class="text-danger">*</span></label>
                                <select name="product_variant_id" id="product_variant_id" class="form-select @error('product_variant_id') is-invalid @enderror" required>
                                    <option value="">— Choisir une variante —</option>
                                    @foreach ($variants as $variant)
                                        <option value="{{ $variant->id }}" @selected(old('product_variant_id') == $variant->id)>
                            {{ $variant->product?->active_translation?->name ?? '#' . $variant->product_id }} — {{ $variant->sku }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_variant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="quantity" class="form-label">Quantité <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="quantity" min="0" step="1" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 0) }}" required />
                                @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="reserved_quantity" class="form-label">Quantité réservée</label>
                                <input type="number" name="reserved_quantity" id="reserved_quantity" min="0" step="1" class="form-control @error('reserved_quantity') is-invalid @enderror" value="{{ old('reserved_quantity', 0) }}" />
                                @error('reserved_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="low_stock_threshold" class="form-label">Seuil d\'alerte <span class="text-danger">*</span></label>
                                <input type="number" name="low_stock_threshold" id="low_stock_threshold" min="0" step="1" class="form-control @error('low_stock_threshold') is-invalid @enderror" value="{{ old('low_stock_threshold', 0) }}" required />
                                @error('low_stock_threshold') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-check me-1"></i> Créer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

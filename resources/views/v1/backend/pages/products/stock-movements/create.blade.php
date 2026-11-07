@extends('v1.backend.layouts.app')

@section('title') {{ __('Créer un mouvement de stock') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Créer un mouvement de stock')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Créer un mouvement de stock') }}</h5>
                    <a href="{{ route('backend.product-stock-movements.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">
                    <form id="create-movement-form" action="{{ route('backend.product-stock-movements.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="warehouse_id" class="form-label">{{ __('Entrepôt') }} <span class="text-danger">*</span></label>
                                <select name="warehouse_id" id="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                    <option value="">{{ __('— Choisir un entrepôt —') }}</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>
                                            {{ $warehouse->name }} ({{ $warehouse->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="product_variant_id" class="form-label">{{ __('Variante') }} <span class="text-danger">*</span></label>
                                <select name="product_variant_id" id="product_variant_id" class="form-select @error('product_variant_id') is-invalid @enderror" required>
                                    <option value="">{{ __('— Choisir une variante —') }}</option>
                                    @foreach ($variants as $variant)
                                        <option value="{{ $variant->id }}" @selected(old('product_variant_id') == $variant->id)>
                                            {{ $variant->product?->active_translation?->name ?? '#' . $variant->product_id }} — {{ $variant->sku }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_variant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">{{ __('Type de mouvement') }} <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">{{ __('— Choisir un type —') }}</option>
                                    @foreach (\App\Enums\Products\StockMovements\ProductStockMovementType::cases() as $type)
                                        <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">{{ __('Entrée / Transfert (entrée) / Retour augmentent le stock — Sortie / Ajustement / Transfert (sortie) le diminuent.') }}</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="quantity" class="form-label">{{ __('Quantité') }} <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="quantity" min="1" step="1" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required />
                                @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">{{ __('Toujours > 0 — le sens est donné par le type.') }}</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="reference_type" class="form-label">{{ __('Type de référence') }}</label>
                                <input type="text" name="reference_type" id="reference_type" class="form-control @error('reference_type') is-invalid @enderror" placeholder="{{ __('ex : order') }}" value="{{ old('reference_type') }}" />
                                @error('reference_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="reference_id" class="form-label">{{ __('ID de référence') }}</label>
                                <input type="number" name="reference_id" id="reference_id" min="1" step="1" class="form-control @error('reference_id') is-invalid @enderror" value="{{ old('reference_id') }}" />
                                @error('reference_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="note" class="form-label">{{ __('Note') }}</label>
                                <textarea name="note" id="note" rows="2" class="form-control @error('note') is-invalid @enderror" placeholder="{{ __('Motif du mouvement…') }}">{{ old('note') }}</textarea>
                                @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <a href="{{ route('backend.product-stock-movements.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i> {{ __('Annuler') }}
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save me-1"></i> {{ __('Enregistrer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

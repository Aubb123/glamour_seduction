<div class="row">
    <div class="col-sm-12">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('backend.product-stock-movements.index') }}" class="row g-2 align-items-end">

                    <div class="col-md-3">
                        <label for="filter-type" class="form-label">{{ __('Type de mouvement') }}</label>
                        <select name="type" id="filter-type" class="form-select">
                            <option value="">{{ __('— Tous les types —') }}</option>
                            @foreach (\App\Enums\Products\StockMovements\ProductStockMovementType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter-warehouse" class="form-label">{{ __('Entrepôt') }}</label>
                        <select name="warehouse_id" id="filter-warehouse" class="form-select">
                            <option value="">{{ __('— Tous les entrepôts —') }}</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" @selected(request('warehouse_id') == $warehouse->id)>
                                    {{ $warehouse->name }} ({{ $warehouse->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter-variant" class="form-label">{{ __('Variante') }}</label>
                        <select name="product_variant_id" id="filter-variant" class="form-select">
                            <option value="">{{ __('— Toutes les variantes —') }}</option>
                            @foreach ($variants as $variant)
                                <option value="{{ $variant->id }}" @selected(request('product_variant_id') == $variant->id)>
                                    {{ $variant->product?->active_translation?->name ?? '#' . $variant->product_id }} — {{ $variant->sku }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bx bx-filter me-1"></i> {{ __('Filtrer') }}
                        </button>
                        <a href="{{ route('backend.product-stock-movements.index') }}" class="btn btn-soft-secondary btn-sm" title="{{ __('Réinitialiser') }}">
                            <i class="bx bx-reset"></i>
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

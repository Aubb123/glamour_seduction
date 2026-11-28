<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    {{ __('Stock par emplacement') }} — <code>{{ Str::limit($productWarehouseLocationStock->variant?->sku ?? '', 30) }}</code>
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.product-warehouse-location-stocks.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                            <li>
                                <a href="{{ route('backend.product-warehouse-location-stocks.edit', ['productWarehouseLocationStock' => $productWarehouseLocationStock->uuid]) }}" class="dropdown-item">
                                    <i class="bx bx-edit me-2 text-primary"></i> {{ __('Modifier') }}
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('backend.product-warehouse-location-stocks.destroy', ['productWarehouseLocationStock' => $productWarehouseLocationStock->uuid]) }}" method="POST" id="delete-form-{{ $productWarehouseLocationStock->uuid }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $productWarehouseLocationStock->uuid }}', '{{ __('Supprimer ce stock par emplacement ?') }}', 'Supprimer', 'warning')">
                                        <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                    </button>
                                </form>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @include('v1.backend.pages.products.warehouse-location-stocks.partials._show-navigation', ['productWarehouseLocationStock' => $productWarehouseLocationStock])
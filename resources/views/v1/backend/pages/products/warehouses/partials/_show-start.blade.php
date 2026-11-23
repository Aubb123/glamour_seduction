<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    {{ __('Entrepôt') }} — <code>{{ Str::limit($warehouse->active_translation?->name ?? $warehouse->code, 30) }}</code>
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.product-warehouses.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                            <li>
                                <a href="{{ route('backend.product-warehouses.edit', ['productWarehouse' => $warehouse->uuid]) }}" class="dropdown-item">
                                    <i class="bx bx-edit me-2 text-primary"></i> {{ __('Modifier') }}
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            @if ($warehouse->status === \App\Enums\Products\Warehouses\ProductWarehouseStatus::Active)
                                <li>
                                    <form action="{{ route('backend.product-warehouses.change_status', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" id="deactivate-form-{{ $warehouse->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="inactive">
                                        <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $warehouse->uuid }}', '{{ __('Désactiver cet entrepôt ?') }}', 'Désactiver', 'warning')">
                                            <i class="bx bx-toggle-left me-2 text-warning"></i> {{ __('Désactiver') }}
                                        </button>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <form action="{{ route('backend.product-warehouses.change_status', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" id="activate-form-{{ $warehouse->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="active">
                                        <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $warehouse->uuid }}', '{{ __('Activer cet entrepôt ?') }}', 'Activer', 'success')">
                                            <i class="bx bx-toggle-right me-2 text-success"></i> {{ __('Activer') }}
                                        </button>
                                    </form>
                                </li>
                            @endif

                            @if (! $warehouse->is_default && $warehouse->type === \App\Enums\Products\Warehouses\ProductWarehouseType::Storage && $warehouse->status === \App\Enums\Products\Warehouses\ProductWarehouseStatus::Active)
                                <li>
                                    <form action="{{ route('backend.product-warehouses.set_default', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" id="default-form-{{ $warehouse->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('default-form-{{ $warehouse->uuid }}', '{{ __('Définir cet entrepôt comme défaut ?') }}', 'Définir', 'warning')">
                                            <i class="bx bx-star me-2 text-warning"></i> {{ __('Définir par défaut') }}
                                        </button>
                                    </form>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" id="delete-form-{{ $warehouse->uuid }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $warehouse->uuid }}', '{{ __('Supprimer cet entrepôt ?') }}', 'Supprimer', 'warning')">
                                        <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                    </button>
                                </form>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a href="{{ route('backend.product-warehouse-translations.trashed', ['warehouse_uuid' => $warehouse->uuid]) }}"
                                    class="dropdown-item text-warning">
                                    <i class="bx bx-trash me-2"></i> {{ __('Corbeille des traductions') }}
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @include('v1.backend.pages.products.warehouses.partials._show-navigation', ['warehouse' => $warehouse])

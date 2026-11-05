<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    {{ __('Entrepôt') }} — <code>{{ $warehouse->name }}</code>
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.product-warehouses.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>

                    <a href="{{ route('backend.product-warehouses.audits', ['productWarehouse' => $warehouse->uuid]) }}" class="btn btn-soft-primary btn-sm">
                        <i class="bx bx-file me-1"></i> {{ __('Audits') }}
                        <span class="badge bg-primary ms-1">{{ $warehouse->audits_count ?? 0 }}</span>
                    </a>

                    <a href="{{ route('backend.product-warehouses.activity-logs', ['productWarehouse' => $warehouse->uuid]) }}" class="btn btn-soft-info btn-sm">
                        <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
                        <span class="badge bg-primary ms-1">{{ $warehouse->activity_logs_count ?? 0 }}</span>
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
                                    <form action="{{ route('backend.product-warehouses.deactivate', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" id="deactivate-form-{{ $warehouse->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $warehouse->uuid }}', '{{ __('Désactiver cet entrepôt ?') }}', '{{ __('Désactiver') }}', 'warning')">
                                            <i class="bx bx-toggle-left me-2 text-warning"></i> {{ __('Désactiver') }}
                                        </button>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <form action="{{ route('backend.product-warehouses.activate', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" id="activate-form-{{ $warehouse->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $warehouse->uuid }}', '{{ __('Activer cet entrepôt ?') }}', '{{ __('Activer') }}', 'success')">
                                            <i class="bx bx-toggle-right me-2 text-success"></i> {{ __('Activer') }}
                                        </button>
                                    </form>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" id="delete-form-{{ $warehouse->uuid }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $warehouse->uuid }}', '{{ __('Supprimer cet entrepôt ?') }}', '{{ __('Supprimer') }}', 'warning')">
                                        <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                    </button>
                                </form>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>

            <div class="card-body">

                @include('v1.backend.pages.products.warehouses.partials._show-navigation', ['warehouse' => $warehouse])
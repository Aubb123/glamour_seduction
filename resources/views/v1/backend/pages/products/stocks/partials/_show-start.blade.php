{{-- En-tête de la fiche stock : titre + actions (retour, audits, logs, menu déroulant).
     Reprend le design de attribute-values/partials/_show-start. --}}
<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    {{ __('Stock') }} — <code>{{ $stock->variant?->sku ?? '—' }}</code>
                    <small class="text-muted ms-2">{{ $stock->warehouse?->name ?? '—' }}</small>
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.product-warehouse-stocks.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>

                    <a href="{{ route('backend.product-warehouse-stocks.audits', ['productWarehouseStock' => $stock->uuid]) }}" class="btn btn-soft-primary btn-sm">
                        <i class="bx bx-file me-1"></i> {{ __('Audits') }}
                        <span class="badge bg-primary ms-1">{{ $stock->audits_count ?? 0 }}</span>
                    </a>

                    <a href="{{ route('backend.product-warehouse-stocks.activity-logs', ['productWarehouseStock' => $stock->uuid]) }}" class="btn btn-soft-info btn-sm">
                        <i class="bx bx-pulse me-1"></i> {{ __("Logs d'activité") }}
                        <span class="badge bg-primary ms-1">{{ $stock->activity_logs_count ?? 0 }}</span>
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                            <li>
                                <a href="{{ route('backend.product-warehouse-stocks.edit', ['productWarehouseStock' => $stock->uuid]) }}" class="dropdown-item">
                                    <i class="bx bx-edit me-2 text-primary"></i> {{ __('Modifier') }}
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('backend.product-warehouse-stocks.destroy', ['productWarehouseStock' => $stock->uuid]) }}" method="POST" id="delete-form-{{ $stock->uuid }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $stock->uuid }}', '{{ __('Supprimer ce stock ?') }}', '{{ __('Supprimer') }}', 'warning')">
                                        <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                    </button>
                                </form>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a href="{{ route('backend.product-warehouse-stocks.trashed') }}" class="dropdown-item text-warning">
                                    <i class="bx bx-trash me-2"></i> {{ __('Corbeille des stocks') }}
                                </a>
                            </li>

                        </ul>
                    </div>

                </div>

            </div>

            <div class="card-body">

                @include('v1.backend.pages.products.stocks.partials._show-navigation', ['stock' => $stock])

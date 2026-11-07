@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset(getEnvFolder() . 'others/backend/datatables/buttons.dataTables.min.css') }}">

    <style>
        .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    </style>
@endpush

@include('v1.backend.layouts.partials._statistics', [
    'statistics' => [
        [
            'label' => __('Stocks'),
            'value' => $stocks->count(),
            'icon'  => 'bx bx-package',
            'color' => 'primary',
        ],
        [
            'label' => __('En stock'),
            'value' => $stocks->where('quantity', '>', 0)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Stock bas'),
            'value' => $stocks->filter(fn ($s) => $s->low_stock_threshold && $s->quantity <= $s->low_stock_threshold)->count(),
            'icon'  => 'bx bx-error-circle',
            'color' => 'warning',
        ],
        [
            'label' => __('Audits'),
            'value' => $stocks->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'info',
        ],
        [
            'label' => __("Logs d'activité"),
            'value' => $stocks->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'danger',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Stocks par entrepôt / variante') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Créer un stock'), 'icon' => 'bx bx-plus', 'route' => 'backend.product-warehouse-stocks.create'],
                                ['label' => __('Liste des stocks'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.product-warehouse-stocks.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.product-warehouse-stocks.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="product-warehouse-stocks-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Entrepôt') }}</th>
                                <th scope="col">{{ __('Produit') }}</th>
                                <th scope="col">{{ __('Variante (SKU)') }}</th>
                                <th scope="col">{{ __('Quantité') }}</th>
                                <th scope="col">{{ __('Réservée') }}</th>
                                <th scope="col">{{ __('Disponible') }}</th>
                                <th scope="col">{{ __('Seuil') }}</th>
                                <th scope="col">{{ __('État') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if(request()->routeIs('backend.product-warehouse-stocks.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stocks as $stock)
                                @php($isLowStock = $stock->low_stock_threshold && $stock->quantity <= $stock->low_stock_threshold)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        @if($stock->warehouse)
                                            <a href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $stock->warehouse->uuid]) }}"
                                               class="badge badge-soft-primary text-decoration-none">
                                                {{ $stock->warehouse->name }}
                                            </a>
                                            <small class="text-muted d-block">{{ $stock->warehouse->code }}</small>
                                        @else
                                            <span class="badge badge-soft-primary">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($stock->variant?->product)
                                            <a href="{{ route('backend.products.show', ['product' => $stock->variant->product->uuid, 'slug' => $stock->variant->product->active_translation?->slug ?? 'null']) }}"
                                               class="text-decoration-none">
                                                {{ $stock->variant->product->active_translation?->name ?? ('#'.$stock->variant->product->id) }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td><code>{{ $stock->variant?->sku ?? '—' }}</code></td>

                                    <td class="fw-bold">{{ $stock->quantity }}</td>
                                    <td class="text-muted">{{ $stock->reserved_quantity }}</td>
                                    <td>
                                        <span class="badge {{ $stock->availableQuantity() > 0 ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                            {{ $stock->availableQuantity() }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($stock->low_stock_threshold)
                                            <span class="badge {{ $isLowStock ? 'badge-soft-warning' : 'badge-soft-secondary' }}">{{ $stock->low_stock_threshold }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($isLowStock)
                                            <span class="badge badge-soft-danger">{{ __('Stock bas') }}</span>
                                        @else
                                            <span class="badge badge-soft-success">{{ __('OK') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-warehouse-stocks.audits', ['productWarehouseStock' => $stock->uuid]) }}" class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $stock->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-warehouse-stocks.activity-logs', ['productWarehouseStock' => $stock->uuid]) }}" class="text-decoration-none">
                                            <span class="badge badge-soft-info">
                                                <i class="bx bx-pulse me-1"></i> {{ $stock->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">@formatDate($stock->created_at, 'D MMMM YYYY - HH:mm:ss')</td>

                                    @if(request()->routeIs('backend.product-warehouse-stocks.trashed'))
                                        <td class="text-muted">@formatDate($stock->deleted_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                    @endif

                                    <td>
                                        @if ($stock->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.product-warehouse-stocks.restore', ['uuid' => $stock->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $stock->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $stock->uuid }}', '{{ __('Restaurer ce stock ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-warehouse-stocks.force-destroy', ['uuid' => $stock->uuid]) }}" method="POST" style="display:inline-block" id="force-delete-form-{{ $stock->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $stock->uuid }}', '{{ __('Supprimer définitivement ce stock ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$stock->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.product-warehouse-stocks.show', ['productWarehouseStock' => $stock->uuid]) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.product-warehouse-stocks.edit', ['productWarehouseStock' => $stock->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                <form action="{{ route('backend.product-warehouse-stocks.destroy', ['productWarehouseStock' => $stock->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $stock->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $stock->uuid }}', '{{ __('Supprimer ce stock ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.product-warehouse-stocks.trashed') ? 14 : 13 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        {{ __('Aucun stock trouvé.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/buttons.print.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#product-warehouse-stocks-table').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>' +
                'tr'                                        +
                '<"row mt-3"<"col-md-5"l><"col-md-7"p>>',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: "{{ app()->getLocale() === 'fr' ? asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/fr-FR.json') : asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/en-GB.json') }}"
                },
            });
        });
    </script>
@endpush

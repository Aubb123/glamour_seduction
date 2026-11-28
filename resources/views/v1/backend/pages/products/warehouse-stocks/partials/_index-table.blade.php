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

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Stocks par entrepôt') }}</h5>
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
                                <th scope="col">{{ __('Variante') }}</th>
                                <th scope="col">{{ __('Produit') }}</th>
                                <th scope="col">{{ __('Entrepôt') }}</th>
                                <th scope="col">{{ __('Quantité') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stocks as $productWarehouseStock)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        <a href="{{ route('backend.product-variants.show', ['productVariant' => $productWarehouseStock->variant?->uuid]) }}" class="text-decoration-none">
                                            {{ $productWarehouseStock->variant?->sku ?? '—' }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.products.show', ['product' => $productWarehouseStock->variant?->product?->uuid, 'slug' => $productWarehouseStock->variant?->product?->active_translation?->slug ?? 'null']) }}" class="text-decoration-none">
                                            {{ $productWarehouseStock->variant?->product?->active_translation?->name ?? '—' }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $productWarehouseStock->warehouse?->uuid, 'slug' => $productWarehouseStock->warehouse?->active_translation?->slug ?? 'null']) }}" class="text-decoration-none">
                                            <span class="badge badge-soft-primary">{{ $productWarehouseStock->warehouse?->active_translation?->name ?? $productWarehouseStock->warehouse?->code ?? '—' }}</span>
                                        </a>
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-secondary">{{ number_format((int) $productWarehouseStock->quantity, 0, '.', ' ') }}</span>
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-primary">
                                            <i class="bx bx-file me-1"></i> {{ $productWarehouseStock->audits_count ?? 0 }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-info">
                                            <i class="bx bx-pulse me-1"></i> {{ $productWarehouseStock->activity_logs_count ?? 0 }}
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($productWarehouseStock->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td>
                                        <div class="hstack gap-2 fs-15">
                                            <a href="{{ route('backend.product-warehouse-stocks.show', ['productWarehouseStock' => $productWarehouseStock->uuid]) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                <i class="bx bx-show"></i>
                                            </a>

                                            <a href="{{ route('backend.product-warehouse-stocks.edit', ['productWarehouseStock' => $productWarehouseStock->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                <i class="bx bx-edit"></i>
                                            </a>

                                            <form action="{{ route('backend.product-warehouse-stocks.destroy', ['productWarehouseStock' => $productWarehouseStock->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $productWarehouseStock->uuid }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $productWarehouseStock->uuid }}', '{{ __('Supprimer ce stock ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
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
                pageLength: {{ (int) getRequiredSettingValue('datatable_default_page_length', app()->getLocale()) }},
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
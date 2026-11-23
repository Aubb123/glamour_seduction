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
            'label' => __('Emplacements'),
            'value' => $locations->count(),
            'icon'  => 'bx bx-map-pin',
            'color' => 'primary',
        ],
        [
            'label' => __('Actifs'),
            'value' => $locations->where('status', \App\Enums\Products\WarehouseLocations\ProductWarehouseLocationStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Zones'),
            'value' => $locations->where('type', \App\Enums\Products\WarehouseLocations\ProductWarehouseLocationType::Zone)->count(),
            'icon'  => 'bx bx-layer',
            'color' => 'info',
        ],
        [
            'label' => __('Étagères'),
            'value' => $locations->where('type', \App\Enums\Products\WarehouseLocations\ProductWarehouseLocationType::Shelf)->count(),
            'icon'  => 'bx bx-grid-alt',
            'color' => 'warning',
        ],
        [
            'label' => __('Audits'),
            'value' => $locations->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'danger',
        ],
        [
            'label' => __('Activités'),
            'value' => $locations->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Emplacements d\'entrepôt') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Créer un emplacement'), 'icon' => 'bx bx-plus', 'route' => 'backend.product-warehouse-locations.create'],
                                ['label' => __('Liste des emplacements'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.product-warehouse-locations.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.product-warehouse-locations.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="product-warehouse-locations-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Code') }}</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col">{{ __('Ordre') }}</th>
                                <th scope="col">{{ __('Parent') }}</th>
                                <th scope="col">{{ __('Entrepôt') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if(request()->routeIs('backend.product-warehouse-locations.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($locations as $location)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <a href="{{ route('backend.product-warehouse-locations.show', ['productWarehouseLocation' => $location->uuid]) }}" class="text-decoration-none text-nowrap">
                                            <code>{{ $location->code }}</code>
                                        </a>
                                    </td>

                                    <td class="fw-bold">
                                        <a href="{{ route('backend.product-warehouse-locations.show', ['productWarehouseLocation' => $location->uuid]) }}" class="text-decoration-none text-nowrap">
                                            {{ Str::limit($location->active_translation?->name ?? '—', 30) }}
                                        </a>
                                    </td>

                                    <td>
                                        <span class="{{ $location->type?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $location->type?->label() ?? '—' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-secondary">{{ $location->sort_order }}</span>
                                    </td>

                                    <td>
                                        @if ($location->parent)
                                            <a href="{{ route('backend.product-warehouse-locations.show', ['productWarehouseLocation' => $location->parent->uuid]) }}"
                                               class="badge badge-soft-info text-decoration-none">
                                                {{ $location->parent->code }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($location->warehouse)
                                            <a href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $location->warehouse->uuid, 'slug' => $location->warehouse->active_translation?->slug ?? 'null']) }}"
                                               class="badge badge-soft-primary text-decoration-none">
                                                {{ $location->warehouse->code }}
                                            </a>
                                        @else
                                            <span class="badge badge-soft-primary">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="{{ $location->status?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $location->status?->label() ?? '—' }}
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-warehouse-locations.audits', ['productWarehouseLocation' => $location->uuid]) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $location->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-warehouse-locations.activity-logs', ['productWarehouseLocation' => $location->uuid]) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-info">
                                                <i class="bx bx-pulse me-1"></i> {{ $location->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($location->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.product-warehouse-locations.trashed'))
                                        <td class="text-muted">
                                            @formatDate($location->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($location->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.product-warehouse-locations.restore', ['uuid' => $location->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $location->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $location->uuid }}', '{{ __('Restaurer cet emplacement ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-warehouse-locations.force_destroy', ['uuid' => $location->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $location->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $location->uuid }}', '{{ __('Supprimer définitivement cet emplacement ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.product-warehouse-locations.show', ['productWarehouseLocation' => $location->uuid]) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.product-warehouse-locations.edit', ['productWarehouseLocation' => $location->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($location->status === \App\Enums\Products\WarehouseLocations\ProductWarehouseLocationStatus::Active)
                                                    <form action="{{ route('backend.product-warehouse-locations.deactivate', ['productWarehouseLocation' => $location->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $location->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-secondary" title="{{ __('Désactiver') }}" onclick="confirmAction('deactivate-form-{{ $location->uuid }}', '{{ __('Désactiver cet emplacement ?') }}', 'Désactiver', 'warning')">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.product-warehouse-locations.activate', ['productWarehouseLocation' => $location->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $location->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Activer') }}" onclick="confirmAction('activate-form-{{ $location->uuid }}', '{{ __('Activer cet emplacement ?') }}', 'Activer', 'success')">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.product-warehouse-locations.destroy', ['productWarehouseLocation' => $location->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $location->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $location->uuid }}', '{{ __('Supprimer cet emplacement ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.product-warehouse-locations.trashed') ? 14 : 13 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        {{ __('Aucun emplacement trouvé.') }}
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
            $('#product-warehouse-locations-table').DataTable({
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

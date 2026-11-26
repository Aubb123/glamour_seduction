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
            'label' => 'Entrepôts',
            'value' => $warehouses->count(),
            'icon'  => 'bx bx-building-house',
            'color' => 'primary',
        ],
        [
            'label' => 'Actifs',
            'value' => $warehouses->where('status', \App\Enums\Products\Warehouses\ProductWarehouseStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => 'Par défaut',
            'value' => $warehouses->where('is_default', true)->count(),
            'icon'  => 'bx bx-star',
            'color' => 'warning',
        ],
        [
            'label' => 'Stockage',
            'value' => $warehouses->where('type', \App\Enums\Products\Warehouses\ProductWarehouseType::Storage)->count(),
            'icon'  => 'bx bx-package',
            'color' => 'info',
        ],
        [
            'label' => 'Audits',
            'value' => $warehouses->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'danger',
        ],
        [
            'label' => 'Activités',
            'value' => $warehouses->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Entrepôts</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Créer un entrepôt', 'icon' => 'bx bx-plus', 'route' => 'backend.product-warehouses.create'],
                                ['label' => 'Liste des entrepôts', 'icon' => 'bx bx-list-ul', 'route' => 'backend.product-warehouses.index'],
                                ['label' => 'Corbeille', 'icon' => 'bx bx-trash', 'route' => 'backend.product-warehouses.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="product-warehouses-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Code</th>
                                <th scope="col">Nom</th>
                                <th scope="col">{{ __('Ordre') }}</th>
                                <th scope="col">Type</th>
                                <th scope="col">Ville</th>
                                <th scope="col">Division</th>
                                <th scope="col">{{ __('Emplacements') }}</th>
                                <th scope="col">Défaut</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Audits</th>
                                <th scope="col">Logs</th>
                                <th scope="col">Créé le</th>
                                @if(request()->routeIs('backend.product-warehouses.trashed'))
                                    <th scope="col">Supprimé le</th>
                                @endif
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td><code>{{ $warehouse->code }}</code></td>

                                    <td class="fw-bold">
                                        <a href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid, 'slug' => $warehouse->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-nowrap">
                                            {{ Str::limit($warehouse->active_translation?->name ?? '-', 30) }}
                                        </a>
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-secondary">{{ $warehouse->sort_order }}</span>
                                    </td>

                                    <td>
                                        <span class="{{ $warehouse->type?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $warehouse->type?->label() ?? '—' }}
                                        </span>
                                    </td>

                                    <td>{{ $warehouse->city ?? '—' }}</td>

                                    <td>
                                        @if($warehouse->division)
                                            <a href="{{ route('backend.geodivisions.show', ['geoDivision' => $warehouse->division->uuid, 'slug' => $warehouse->division->active_translation?->slug ?? 'null']) }}"
                                               class="badge badge-soft-primary text-decoration-none">
                                                {{ $warehouse->division->active_translation?->name ?? '—' }}
                                            </a>
                                        @else
                                            <span class="badge badge-soft-primary">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-warehouses.locations', ['productWarehouse' => $warehouse->uuid, 'slug' => $warehouse->active_translation?->slug ?? 'null']) }}" class="text-decoration-none">
                                            <span class="badge badge-soft-secondary">
                                                <i class="bx bx-map-pin me-1"></i> {{ $warehouse->locations_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        @if ($warehouse->is_default)
                                            <span class="badge badge-soft-warning"><i class="bx bx-star me-1"></i> Défaut</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="{{ $warehouse->status?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $warehouse->status?->label() ?? '—' }}
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-warehouses.audits', ['productWarehouse' => $warehouse->uuid, 'slug' => $warehouse->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $warehouse->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-warehouses.activity-logs', ['productWarehouse' => $warehouse->uuid, 'slug' => $warehouse->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-info">
                                                <i class="bx bx-pulse me-1"></i> {{ $warehouse->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($warehouse->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.product-warehouses.trashed'))
                                        <td class="text-muted">
                                            @formatDate($warehouse->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($warehouse->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.product-warehouses.restore', ['uuid' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $warehouse->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="Restaurer" onclick="confirmAction('restore-form-{{ $warehouse->uuid }}', 'Restaurer cet entrepôt ?', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-warehouses.force_destroy', ['uuid' => $warehouse->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $warehouse->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer définitivement" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $warehouse->uuid }}', 'Supprimer définitivement cet entrepôt ? Cette action est irréversible.')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid, 'slug' => $warehouse->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-light" title="Voir">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.product-warehouses.edit', ['productWarehouse' => $warehouse->uuid]) }}" class="btn btn-sm btn-soft-primary" title="Modifier">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if (! $warehouse->is_default && $warehouse->type === \App\Enums\Products\Warehouses\ProductWarehouseType::Storage && $warehouse->status === \App\Enums\Products\Warehouses\ProductWarehouseStatus::Active)
                                                    <form action="{{ route('backend.product-warehouses.set_default', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="default-form-{{ $warehouse->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" title="Définir par défaut" onclick="confirmAction('default-form-{{ $warehouse->uuid }}', 'Définir cet entrepôt comme défaut ?', 'Définir', 'warning')">
                                                            <i class="bx bx-star"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if ($warehouse->status === \App\Enums\Products\Warehouses\ProductWarehouseStatus::Active)
                                                    <form action="{{ route('backend.product-warehouses.change_status', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $warehouse->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="inactive">
                                                        <button type="button" class="btn btn-sm btn-soft-secondary" title="Désactiver" onclick="confirmAction('deactivate-form-{{ $warehouse->uuid }}', 'Désactiver cet entrepôt ?', 'Désactiver', 'warning')">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.product-warehouses.change_status', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $warehouse->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="active">
                                                        <button type="button" class="btn btn-sm btn-soft-success" title="Activer" onclick="confirmAction('activate-form-{{ $warehouse->uuid }}', 'Activer cet entrepôt ?', 'Activer', 'success')">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $warehouse->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $warehouse->uuid }}', 'Supprimer cet entrepôt ?', 'Supprimer', 'warning')" title="Supprimer">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.product-warehouses.trashed') ? 15 : 14 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        Aucun entrepôt trouvé.
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
            $('#product-warehouses-table').DataTable({
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



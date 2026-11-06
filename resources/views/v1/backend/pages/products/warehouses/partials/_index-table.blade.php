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
            'label' => __('Entrepôts'),
            'value' => $warehouses->count(),
            'icon'  => 'bx bx-building-house',
            'color' => 'primary',
        ],
        [
            'label' => __('Actifs'),
            'value' => $warehouses->where('status', \App\Enums\Products\Warehouses\ProductWarehouseStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Inactifs'),
            'value' => $warehouses->where('status', \App\Enums\Products\Warehouses\ProductWarehouseStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Par défaut'),
            'value' => $warehouses->where('is_default', true)->count(),
            'icon'  => 'bx bx-star',
            'color' => 'info',
        ],
        [
            'label' => __('Audits'),
            'value' => $warehouses->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'secondary',
        ],
        [
            'label' => __("Logs d'activité"),
            'value' => $warehouses->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'warning',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Liste des entrepôts') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Nouvel entrepôt'), 'icon' => 'bx bx-plus', 'route' => 'backend.product-warehouses.create'],
                                ['label' => __('Liste des entrepôts'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.product-warehouses.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.product-warehouses.trashed'],
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
                                <th scope="col">{{ __('Code') }}</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Ville') }}</th>
                                <th scope="col">{{ __('Téléphone') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Par défaut') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if (request()->routeIs('backend.product-warehouses.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col" class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <span class="fw-semibold"><code>{{ $warehouse->code }}</code></span>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid]) }}"
                                           class="text-decoration-none text-nowrap fw-semibold">
                                            {{ $warehouse->name }}
                                        </a>
                                    </td>

                                    <td>{{ $warehouse->city ?? '—' }}</td>

                                    <td>
                                        @if ($warehouse->phone)
                                            <a href="tel:{{ $warehouse->phone }}" class="text-decoration-none">{{ $warehouse->phone }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td>
                                        <span class="{{ $warehouse->status?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $warehouse->status?->label() ?? '—' }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($warehouse->is_default)
                                            <span class="badge badge-soft-success">{{ __('Par défaut') }}</span>
                                        @else
                                            <span class="badge badge-soft-secondary">—</span>
                                        @endif
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($warehouse->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if (request()->routeIs('backend.product-warehouses.trashed'))
                                        <td class="text-muted">
                                            @formatDate($warehouse->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td class="text-center">
                                        @if ($warehouse->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.product-warehouses.restore', ['uuid' => $warehouse->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $warehouse->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $warehouse->uuid }}', '{{ __('Restaurer cet entrepôt ?') }}', '{{ __('Restaurer') }}', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-warehouses.force_destroy', ['uuid' => $warehouse->uuid]) }}" method="POST" style="display:inline-block" id="force-delete-form-{{ $warehouse->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" onclick="confirmAction('force-delete-form-{{ $warehouse->uuid }}', '{{ __('Supprimer définitivement cet entrepôt ?') }}', '{{ __('Supprimer définitivement') }}', 'danger')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid]) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.product-warehouses.edit', ['productWarehouse' => $warehouse->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($warehouse->status === \App\Enums\Products\Warehouses\ProductWarehouseStatus::Active)
                                                    <form action="{{ route('backend.product-warehouses.deactivate', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $warehouse->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" title="{{ __('Désactiver') }}" onclick="confirmAction('deactivate-form-{{ $warehouse->uuid }}', '{{ __('Désactiver cet entrepôt ?') }}', '{{ __('Désactiver') }}', 'warning')">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.product-warehouses.activate', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $warehouse->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Activer') }}" onclick="confirmAction('activate-form-{{ $warehouse->uuid }}', '{{ __('Activer cet entrepôt ?') }}', '{{ __('Activer') }}', 'success')">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $warehouse->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer') }}" onclick="confirmAction('delete-form-{{ $warehouse->uuid }}', '{{ __('Supprimer cet entrepôt ?') }}', '{{ __('Supprimer') }}', 'warning')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.product-warehouses.trashed') ? 9 : 8 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        {{ __('Aucun entrepôt trouvé.') }}
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

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset(getEnvFolder() . 'others/backend/datatables/dataTables.bootstrap5.min.css') }}">

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
            'label' => __('Logs d\'activité'),
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
                                ['label' => __('Nouvel entrepôt'), 'icon' => 'bx bx-plus', 'route' => 'backend.product-warehouses.create', 'permission' => 'product-warehouses.create'],
                                ['label' => __('Liste des entrepôts'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.product-warehouses.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.product-warehouses.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle w-100" id="product-warehouses-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Nom') }}</th>
                                <th>{{ __('Ville') }}</th>
                                <th>{{ __('Téléphone') }}</th>
                                <th>{{ __('Statut') }}</th>
                                <th>{{ __('Par défaut') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="fw-semibold"><code>{{ $warehouse->code }}</code></span></td>
                                    <td>{{ $warehouse->name }}</td>
                                    <td>{{ $warehouse->city ?? '—' }}</td>
                                    <td>{{ $warehouse->phone ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $warehouse->status->badgeClass() }} fs-14 px-3 py-2">
                                            {{ $warehouse->status->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($warehouse->is_default)
                                            <span class="badge badge-soft-success">{{ __('Par défaut') }}</span>
                                        @else
                                            <span class="badge badge-soft-secondary">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            @if (request()->routeIs('backend.product-warehouses.trashed'))

                                                <form action="{{ route('backend.product-warehouses.restore', ['uuid' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $warehouse->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $warehouse->uuid }}', '{{ __('Restaurer cet entrepôt ?') }}', '{{ __('Restaurer') }}', 'success')">
                                                        <i class="bx bx-rotate-left"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-warehouses.force-destroy', ['uuid' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="force-delete-form-{{ $warehouse->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" onclick="confirmAction('force-delete-form-{{ $warehouse->uuid }}', '{{ __('Supprimer définitivement cet entrepôt ?') }}', '{{ __('Supprimer définitivement') }}', 'danger')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            @else

                                                <a href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid]) }}" class="btn btn-sm btn-soft-secondary" title="{{ __('Détails') }}">
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

                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bx bx-building-house fs-3 d-block mb-2"></i>
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
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#product-warehouses-table').DataTable({
                language: { url: '{{ asset(getEnvFolder() . 'others/backend/datatables/French.json') }}' },
                order: [[0, 'asc']],
                pageLength: 25,
            });
        });
    </script>
@endpush

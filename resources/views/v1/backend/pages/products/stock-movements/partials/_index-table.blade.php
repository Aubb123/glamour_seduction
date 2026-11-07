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
            'label' => __('Mouvements'),
            'value' => $movements->count(),
            'icon'  => 'bx bx-transfer',
            'color' => 'primary',
        ],
        [
            'label' => __('Entrées'),
            'value' => $movements->filter(fn ($m) => ! $m->type->decreasesStock())->count(),
            'icon'  => 'bx bx-import',
            'color' => 'success',
        ],
        [
            'label' => __('Sorties'),
            'value' => $movements->filter(fn ($m) => $m->type->decreasesStock())->count(),
            'icon'  => 'bx bx-export',
            'color' => 'danger',
        ],
        [
            'label' => __('Audits'),
            'value' => $movements->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'info',
        ],
        [
            'label' => __("Logs d'activité"),
            'value' => $movements->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'warning',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Mouvements de stock') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Nouveau mouvement'), 'icon' => 'bx bx-plus', 'route' => 'backend.product-stock-movements.create'],
                                ['label' => __('Liste des mouvements'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.product-stock-movements.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.product-stock-movements.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="product-stock-movements-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Date') }}</th>
                                <th scope="col">{{ __('Entrepôt') }}</th>
                                <th scope="col">{{ __('Produit') }}</th>
                                <th scope="col">{{ __('Variante (SKU)') }}</th>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col">{{ __('Quantité') }}</th>
                                <th scope="col">{{ __('Note') }}</th>
                                <th scope="col">{{ __('Par') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs') }}</th>
                                @if(request()->routeIs('backend.product-stock-movements.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($movements as $movement)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="text-muted">
                                        @formatDate($movement->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td>
                                        @if ($movement->warehouse)
                                            <span class="fw-semibold">{{ $movement->warehouse->name }}</span>
                                            <small class="text-muted d-block">{{ $movement->warehouse->code }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td class="fw-bold">
                                        {{ $movement->variant?->product?->active_translation?->name ?? '—' }}
                                    </td>

                                    <td><code>{{ $movement->variant?->sku ?? '—' }}</code></td>

                                    <td>
                                        <span class="{{ $movement->type->badgeClass() }}">{{ $movement->type->label() }}</span>
                                    </td>

                                    <td>
                                        @if ($movement->type->decreasesStock())
                                            <span class="text-danger fw-semibold">−{{ $movement->quantity }}</span>
                                        @else
                                            <span class="text-success fw-semibold">+{{ $movement->quantity }}</span>
                                        @endif
                                    </td>

                                    <td class="text-truncate" style="max-width: 220px;" title="{{ $movement->note }}">
                                        {{ $movement->note ?? '—' }}
                                    </td>

                                    <td>
                                        @php $reporter = $movement->createdBy; @endphp
                                        {{ $reporter ? $reporter->first_name.' '.$reporter->last_name : '—' }}
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-stock-movements.audits', ['productStockMovement' => $movement->uuid]) }}" class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $movement->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-stock-movements.activity-logs', ['productStockMovement' => $movement->uuid]) }}" class="text-decoration-none">
                                            <span class="badge badge-soft-info">
                                                <i class="bx bx-pulse me-1"></i> {{ $movement->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    @if(request()->routeIs('backend.product-stock-movements.trashed'))
                                        <td class="text-muted">
                                            @formatDate($movement->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($movement->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.product-stock-movements.restore', ['uuid' => $movement->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $movement->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $movement->uuid }}', '{{ __('Restaurer ce mouvement ?') }}', '{{ __('Restaurer') }}', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-stock-movements.force-destroy', ['uuid' => $movement->uuid]) }}" method="POST" style="display:inline-block" id="force-destroy-form-{{ $movement->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" onclick="confirmAction('force-destroy-form-{{ $movement->uuid }}', '{{ __('Supprimer définitivement ce mouvement ? Cette action est irréversible.') }}', '{{ __('Supprimer définitivement') }}', 'danger')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$movement->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.product-stock-movements.show', ['productStockMovement' => $movement->uuid]) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <form action="{{ route('backend.product-stock-movements.destroy', ['productStockMovement' => $movement->uuid]) }}" method="POST" class="d-inline" id="destroy-form-{{ $movement->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('destroy-form-{{ $movement->uuid }}', '{{ __('Supprimer ce mouvement ?') }}', '{{ __('Supprimer') }}', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.product-stock-movements.trashed') ? 13 : 12 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-transfer fs-3 d-block mb-2"></i>
                                        {{ __('Aucun mouvement de stock trouvé.') }}
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
            $('#product-stock-movements-table').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>' +
                'tr'                                        +
                '<"row mt-3"<"col-md-5"l><"col-md-7"p>>',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[1, 'desc']],
                pageLength: 25,
                language: {
                    url: "{{ app()->getLocale() === 'fr' ? asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/fr-FR.json') : asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/en-GB.json') }}"
                },
            });
        });
    </script>
@endpush

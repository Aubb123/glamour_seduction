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
            'label' => __('Boutiques'),
            'value' => $shops->count(),
            'icon'  => 'bx bx-store',
            'color' => 'primary',
        ],
        [
            'label' => __('Actives'),
            'value' => $shops->where('status', \App\Enums\Shops\Shops\ShopStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Inactives'),
            'value' => $shops->where('status', \App\Enums\Shops\Shops\ShopStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Par défaut'),
            'value' => $shops->where('is_default', true)->count(),
            'icon'  => 'bx bx-star',
            'color' => 'info',
        ],
        [
            'label' => __('Audits'),
            'value' => $shops->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'secondary',
        ],
        [
            'label' => __("Logs d'activité"),
            'value' => $shops->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'warning',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Liste des boutiques') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Nouvelle boutique'), 'icon' => 'bx bx-plus', 'route' => 'backend.shops.create'],
                                ['label' => __('Liste des boutiques'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.shops.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.shops.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="shops-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Code') }}</th>
                                <th scope="col">{{ __('Ville') }}</th>
                                <th scope="col">{{ __('Entrepôts') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Par défaut') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if (request()->routeIs('backend.shops.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col" class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($shops as $shop)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <a href="{{ route('backend.shops.show', ['shop' => $shop->uuid, 'slug' => $shop->active_translation?->slug ?? 'null']) }}"
                                           class="fw-semibold text-decoration-none text-nowrap">
                                            {{ $shop->active_translation?->name ?? __('Sans nom') }}
                                        </a>
                                    </td>

                                    <td><span class="fw-semibold"><code>{{ $shop->code }}</code></span></td>

                                    <td>{{ $shop->city ?? '—' }}</td>

                                    <td>
                                        <span class="badge badge-soft-primary">{{ $shop->warehouses->count() }}</span>
                                        @if ($primary = $shop->warehouses->firstWhere('pivot.is_primary', true))
                                            <small class="text-muted d-block">{{ __('Principal') }} : {{ $primary->code }}</small>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="{{ $shop->status?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $shop->status?->label() ?? '—' }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($shop->is_default)
                                            <span class="badge badge-soft-success">{{ __('Par défaut') }}</span>
                                        @else
                                            <span class="badge badge-soft-secondary">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.shops.audits', ['shop' => $shop->uuid, 'slug' => $shop->active_translation?->slug ?? 'null']) }}" class="text-decoration-none">
                                            <span class="badge badge-soft-primary"><i class="bx bx-file me-1"></i> {{ $shop->audits_count ?? 0 }}</span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.shops.activity-logs', ['shop' => $shop->uuid, 'slug' => $shop->active_translation?->slug ?? 'null']) }}" class="text-decoration-none">
                                            <span class="badge badge-soft-info"><i class="bx bx-pulse me-1"></i> {{ $shop->activity_logs_count ?? 0 }}</span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($shop->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if (request()->routeIs('backend.shops.trashed'))
                                        <td class="text-muted">
                                            @formatDate($shop->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td class="text-center">
                                        @if ($shop->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.shops.restore', ['uuid' => $shop->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $shop->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $shop->uuid }}', '{{ __('Restaurer cette boutique ?') }}', '{{ __('Restaurer') }}', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.shops.force_destroy', ['uuid' => $shop->uuid]) }}" method="POST" style="display:inline-block" id="force-delete-form-{{ $shop->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" onclick="confirmAction('force-delete-form-{{ $shop->uuid }}', '{{ __('Supprimer définitivement cette boutique ? Cette action est irréversible.') }}', '{{ __('Supprimer définitivement') }}', 'danger')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.shops.show', ['shop' => $shop->uuid, 'slug' => $shop->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.shops.edit', ['shop' => $shop->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($shop->status === \App\Enums\Shops\Shops\ShopStatus::Active)
                                                    <form action="{{ route('backend.shops.deactivate', ['shop' => $shop->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $shop->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" title="{{ __('Désactiver') }}" onclick="confirmAction('deactivate-form-{{ $shop->uuid }}', '{{ __('Désactiver cette boutique ?') }}', '{{ __('Désactiver') }}', 'warning')">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.shops.activate', ['shop' => $shop->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $shop->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Activer') }}" onclick="confirmAction('activate-form-{{ $shop->uuid }}', '{{ __('Activer cette boutique ?') }}', '{{ __('Activer') }}', 'success')">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.shops.destroy', ['shop' => $shop->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $shop->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer') }}" onclick="confirmAction('delete-form-{{ $shop->uuid }}', '{{ __('Supprimer cette boutique ?') }}', '{{ __('Supprimer') }}', 'warning')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.shops.trashed') ? 12 : 11 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-store fs-3 d-block mb-2"></i>
                                        {{ __('Aucune boutique trouvée.') }}
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
            $('#shops-table').DataTable({
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

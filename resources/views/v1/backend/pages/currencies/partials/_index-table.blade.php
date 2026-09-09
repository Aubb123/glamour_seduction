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
            'label' => __('v1/backend/views/pages/currencies/partials/_index-table.devises'),
            'value' => $currencies->count(),
            'icon'  => 'bx bx-coin',
            'color' => 'primary',
        ],
        [
            'label' => __('v1/backend/views/pages/currencies/partials/_index-table.actives'),
            'value' => $currencies->where('status', \App\Enums\Currencies\CurrencyStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('v1/backend/views/pages/currencies/partials/_index-table.inactives'),
            'value' => $currencies->where('status', \App\Enums\Currencies\CurrencyStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('v1/backend/views/pages/currencies/partials/_index-table.pays'),
            'value' => $currencies->sum('countries_count'),
            'icon'  => 'bx bx-map',
            'color' => 'info',
        ],
        [
            'label' => __('v1/backend/views/pages/currencies/partials/_index-table.audits'),
            'value' => $currencies->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => __('v1/backend/views/pages/currencies/partials/_index-table.activites'),
            'value' => $currencies->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('v1/backend/views/pages/currencies/partials/_index-table.devises') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('v1/backend/views/pages/currencies/partials/_index-table.actions') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('v1/backend/views/pages/currencies/partials/_index-table.listeDesDevises'),   'icon' => 'bx bx-list-ul',   'route' => 'backend.currencies.index'],
                                ['label' => __('v1/backend/views/pages/currencies/partials/_index-table.corbeille'),        'icon' => 'bx bx-trash', 'route' => 'backend.currencies.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="currencies-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.symbole') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.nom') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.code') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.codeNumerique') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.decimales') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.paysUtilisent') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.ordre') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.defaut') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.actif') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.audits') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.logsActivite') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.creeLe') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.modifieLe') }}</th>
                                @if(request()->routeIs('backend.currencies.trashed'))
                                    <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.supprimeLe') }}</th>
                                @endif
                                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_index-table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($currencies as $currency)
                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-success fs-5 fw-bold">{{ $currency->symbol }}</span>
                                    </td>

                                    <td class="fw-bold">
                                        {{ $currency->active_translation?->name ?? '—' }}
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-primary">{{ $currency->code }}</span>
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-secondary">{{ $currency->numeric_code ?? '—' }}</span>
                                    </td>

                                    <td>
                                        {{ $currency->decimal_places }}
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.currencies.countries', ['currency' => $currency->uuid, 'slug' => $currency->active_translation?->slug ?? $currency->code]) }}"
                                            class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-map me-1"></i>{{ $currency->countries_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        {{ $currency->sort_order }}
                                    </td>

                                    <td class="text-center">
                                        @if ($currency->is_default)
                                            <span class="badge bg-soft-success"><i class="bx bx-star me-1"></i>{{ __('v1/backend/views/pages/currencies/partials/_index-table.oui') }}</span>
                                        @else
                                            <span class="badge badge-soft-secondary">{{ __('v1/backend/views/pages/currencies/partials/_index-table.non') }}</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <span class="badge {{ $currency->status?->badgeClass() }}">
                                            {{ $currency->status?->label() }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.currencies.audits', ['currency' => $currency->uuid, 'slug' => $currency->active_translation?->slug ?? $currency->code]) }}"
                                            class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i>{{ $currency->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.currencies.activity-logs', ['currency' => $currency->uuid, 'slug' => $currency->active_translation?->slug ?? $currency->code]) }}"
                                            class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-pulse me-1"></i>{{ $currency->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($currency->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($currency->updated_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.currencies.trashed'))
                                        <td class="text-muted">
                                            @formatDate($currency->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($currency->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <form action="{{ route('backend.currencies.restore', ['uuid' => $currency->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $currency->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('v1/backend/views/pages/currencies/partials/_index-table.restaurer') }}" onclick="confirmAction('restore-form-{{ $currency->uuid }}', '{{ __('v1/backend/views/pages/currencies/partials/_index-table.restaurerCetteDevise') }}', '{{ __('v1/backend/views/pages/currencies/partials/_index-table.restaurer') }}', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.currencies.force_destroy', ['uuid' => $currency->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $currency->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('v1/backend/views/pages/currencies/partials/_index-table.supprimerDefinitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $currency->uuid }}', '{{ __('v1/backend/views/pages/currencies/partials/_index-table.confirmerSuppressionDefinitive') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        @endif

                                        @if (!$currency->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <a href="{{ route('backend.currencies.show', ['currency' => $currency->uuid, 'slug' => $currency->active_translation?->slug ?? $currency->code]) }}"
                                                   class="btn btn-sm btn-light" title="{{ __('v1/backend/views/pages/currencies/partials/_index-table.voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.currencies.edit', ['currency' => $currency->uuid]) }}"
                                                   class="btn btn-sm btn-soft-primary" title="{{ __('v1/backend/views/pages/currencies/partials/_index-table.modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if (! $currency->is_default)
                                                    <form action="{{ route('backend.currencies.destroy', ['currency' => $currency->uuid]) }}" method="POST" style="display:inline-block" id="row-destroy-form-{{ $currency->uuid }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('v1/backend/views/pages/currencies/partials/_index-table.supprimer') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $currency->uuid }}', '{{ __('v1/backend/views/pages/currencies/partials/_index-table.confirmerSuppression') }}')">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.currencies.trashed') ? 16 : 15 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        {{ __('v1/backend/views/pages/currencies/partials/_index-table.aucuneDeviseTrouvee') }}
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
            $('#currencies-table').DataTable({
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

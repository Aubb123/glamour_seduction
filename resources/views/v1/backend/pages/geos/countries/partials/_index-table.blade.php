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
            'label' => __('v1/backend/views/pages/geos/countries/partials/_index-table.pays'),
            'value' => $geocountries->count(),
            'icon'  => 'bx bx-map',
            'color' => 'primary',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/countries/partials/_index-table.actifs'),
            'value' => $geocountries->where('status', \App\Enums\Geos\Countries\GeoCountryStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/countries/partials/_index-table.inactifs'),
            'value' => $geocountries->where('status', \App\Enums\Geos\Countries\GeoCountryStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/countries/partials/_index-table.utilisateurs'),
            'value' => $geocountries->sum('users_count'),
            'icon'  => 'bx bx-user',
            'color' => 'info',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/countries/partials/_index-table.audits'),
            'value' => $geocountries->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/countries/partials/_index-table.activites'),
            'value' => $geocountries->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.pays') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('v1/backend/views/pages/geos/countries/partials/_index-table.actions') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('v1/backend/views/pages/geos/countries/partials/_index-table.listeDesPays'),   'icon' => 'bx bx-list-ul',   'route' => 'backend.geocountries.index'],
                                ['label' => __('v1/backend/views/pages/geos/countries/partials/_index-table.corbeille'),        'icon' => 'bx bx-trash', 'route' => 'backend.geocountries.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="countries-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.drapeau') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.nom') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.code') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.code2') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.indicatif') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.devise') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.type') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.continent') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.utilisateurs') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.ordre') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.actif') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.audits') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.logsActivite') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.creeLe') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.modifieLe') }}</th>
                                @if(request()->routeIs('backend.geocountries.trashed'))
                                    <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.supprimeLe') }}</th>
                                @endif
                                <th scope="col">{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($geocountries as $country)
                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <a href="{{ asset(getEnvFolder() . $country->file_path) }}" ttarget="_blank" rrel="noopener noreferrer">
                                            <img src="{{ asset(getEnvFolder() . $country->file_path) }}" alt="{{ $country->active_translation?->name ?? $country->code }}" style="width: 32px; height: 22px; object-fit: cover; border-radius: 3px;">
                                        </a>
                                    </td>

                                    <td class="fw-bold">
                                        {{ $country->active_translation?->name ?? '—' }}
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-primary">{{ $country->code }}</span>
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-secondary">{{ $country->code_2 }}</span>
                                    </td>

                                    <td class="text-muted">
                                        {{ $country->phone_code ?? '—' }}
                                    </td>

                                    <td>
                                        @if ($country->currency_code)
                                            <span class="badge badge-soft-success" title="{{ $country->currency_name }}">{{ $country->currency_symbol }} {{ $country->currency_code }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-info">{{ $country->type_label }}</span>
                                    </td>

                                    <td class="text-muted">
                                        @if($country->continent)
                                            <a href="{{ route('backend.geocontinents.show', ['geoContinent' => $country->continent->uuid, 'slug' => $country->continent->active_translation?->slug ?? 'null']) }}"
                                                class="fw-semibold text-primary text-decoration-none">
                                                {{ $country->continent->active_translation?->name ?? $country->continent->code }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.geocountries.users', ['geoCountry' => $country->uuid, 'slug' => $country->active_translation?->slug ?? $country->code]) }}"
                                            class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-user me-1"></i>{{ $country->users_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        {{ $country->sort_order }}
                                    </td>

                                    <td>
                                        <span class="{{ $country->status?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $country->status?->label() ?? __('v1/backend/views/pages/geos/countries/partials/_index-table.inconnu') }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.geocountries.audits', ['geoCountry' => $country->uuid, 'slug' => $country->active_translation?->slug ?? $country->code]) }}"
                                            class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i>{{ $country->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.geocountries.activity-logs', ['geoCountry' => $country->uuid, 'slug' => $country->active_translation?->slug ?? $country->code]) }}"
                                            class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-pulse me-1"></i>{{ $country->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($country->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($country->updated_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.geocountries.trashed'))
                                        <td class="text-muted">
                                            @formatDate($country->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($country->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <form action="{{ route('backend.geocountries.restore', ['uuid' => $country->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $country->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.restaurer') }}" onclick="confirmAction('restore-form-{{ $country->uuid }}', '{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.restaurerCePays') }}', '{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.restaurer') }}', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.geocountries.force_destroy', ['uuid' => $country->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $country->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.supprimerDefinitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $country->uuid }}', '{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.confirmerSuppressionDefinitive') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        @endif

                                        @if (!$country->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <a href="{{ route('backend.geocountries.show', ['geoCountry' => $country->uuid, 'slug' => $country->active_translation?->slug ?? $country->code]) }}"
                                                   class="btn btn-sm btn-light" title="{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.geocountries.edit', ['geoCountry' => $country->uuid]) }}"
                                                   class="btn btn-sm btn-soft-primary" title="{{ __('v1/backend/views/pages/geos/countries/partials/_index-table.modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.geocountries.trashed') ? 18 : 17 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('v1/backend/views/pages/geos/countries/partials/_index-table.aucunPaysTrouve') }}
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
            $('#countries-table').DataTable({
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
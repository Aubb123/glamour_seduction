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
            'label' => __('Continents'),
            'value' => $geocontinents->count(),
            'icon'  => 'bx bx-globe',
            'color' => 'primary',
        ],
        [
            'label' => __('Actifs'),
            'value' => $geocontinents->where('status', \App\Enums\Geos\Continents\GeoContinentStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Inactifs'),
            'value' => $geocontinents->where('status', \App\Enums\Geos\Continents\GeoContinentStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Pays'),
            'value' => $geocontinents->sum('countries_count'),
            'icon'  => 'bx bx-map',
            'color' => 'info',
        ],
        [
            'label' => __('Audits'),
            'value' => $geocontinents->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => __('Activités'),
            'value' => $geocontinents->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Continents') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Liste des continents', 'icon' => 'bx bx-list-ul',  'route' => 'backend.geocontinents.index'],
                                ['label' => 'Corbeille',          'icon' => 'bx bx-trash', 'route' => 'backend.geocontinents.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="geocontinents-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Image') }}</th>
                                <th scope="col">{{ __('Code') }}</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Population') }}</th>
                                <th scope="col">{{ __('Superficie (km²)') }}</th>
                                <th scope="col">{{ __('Pays') }}</th>
                                <th scope="col">{{ __('Ordre') }}</th>
                                <th scope="col">{{ __('Actif') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs d\'activité') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                <th scope="col">{{ __('Modifié le') }}</th>
                                @if(request()->routeIs('backend.geocontinents.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($geocontinents as $continent)
                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <a href="{{ asset(getEnvFolder() . $continent->file_path) }}" ttarget="_blank" rrel="noopener noreferrer">
                                            <img src="{{ asset(getEnvFolder() . $continent->file_path) }}" alt="{{ $continent->active_translation?->name ?? $continent->code }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        </a>
                                    </td>

                                    <td class="fw-bold">
                                        <span class="badge badge-soft-secondary fs-12">{{ strtoupper($continent->code) }}</span>
                                    </td>

                                    <td class="fw-bold">
                                        <a href="{{ route('backend.geocontinents.show', ['geoContinent' => $continent->uuid, 'slug' => $continent->active_translation?->slug ?? 'null']) }}"
                                            class="text-decoration-none text-primary">
                                            {{ $continent->active_translation?->name ?? '—' }}
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        {{ $continent->population ? number_format($continent->population, 0, ',', ' ') : '—' }}
                                    </td>

                                    <td class="text-muted">
                                        {{ $continent->area_km2 ? number_format($continent->area_km2, 2, ',', ' ') . ' km²' : '—' }}
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.geocontinents.countries', ['geoContinent' => $continent->uuid, 'slug' => $continent->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none text-primary">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-map me-1"></i>{{ $continent->countries_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-center text-muted">
                                        {{ $continent->sort_order }}
                                    </td>

                                    <td>
                                        <span class="{{ $continent->status?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $continent->status?->label() ?? __('Inconnu') }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.geocontinents.audits', ['geoContinent' => $continent->uuid, 'slug' => $continent->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none text-primary">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i>{{ $continent->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.geocontinents.activity-logs', ['geoContinent' => $continent->uuid, 'slug' => $continent->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none text-primary">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-pulse me-1"></i>{{ $continent->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($continent->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($continent->updated_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.geocontinents.trashed'))
                                        <td class="text-muted">
                                            @formatDate($continent->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($continent->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <form action="{{ route('backend.geocontinents.restore', ['uuid' => $continent->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $continent->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $continent->uuid }}', '{{ __('Restaurer ce continent ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.geocontinents.force_destroy', ['uuid' => $continent->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $continent->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;"
                                                            onclick="confirmDelete('{{ $continent->uuid }}', '{{ __('Supprimer définitivement ce continent ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        @endif

                                        @if (!$continent->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.geocontinents.show', ['geoContinent' => $continent->uuid, 'slug' => $continent->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a href="{{ route('backend.geocontinents.edit', ['geoContinent' => $continent->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.geocontinents.trashed') ? 11 : 10 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucun continent trouvé.') }}
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
            $('#geocontinents-table').DataTable({
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
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
            'label' => __('v1/backend/views/pages/geos/divisions/partials/_index-table.divisions'),
            'value' => $divisions->count(),
            'icon'  => 'bx bx-map',
            'color' => 'primary',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/divisions/partials/_index-table.actifs'),
            'value' => $divisions->where('status', \App\Enums\Geos\Divisions\GeoDivisionStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/divisions/partials/_index-table.inactifs'),
            'value' => $divisions->where('status', \App\Enums\Geos\Divisions\GeoDivisionStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/divisions/partials/_index-table.audits'),
            'value' => $divisions->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/divisions/partials/_index-table.activites'),
            'value' => $divisions->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.listeDesDivisions') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.actions') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('v1/backend/views/pages/geos/divisions/partials/_index-table.creerUneDivision'), 'icon' => 'bx bx-plus', 'route' => 'backend.geodivisions.create'],
                                ['label' => __('v1/backend/views/pages/geos/divisions/partials/_index-table.listeDesDivisions'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.geodivisions.index'],
                                ['label' => __('v1/backend/views/pages/geos/divisions/partials/_index-table.corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.geodivisions.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="divisions-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.nom') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.type') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.niveau') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.parent') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.pays') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.statut') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.audits') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.logs') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.creeLe') }}</th>
                                @if(request()->routeIs('backend.geodivisions.trashed'))
                                    <th scope="col">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.supprimeLe') }}</th>
                                @endif
                                <th scope="col">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($divisions as $division)
                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="fw-bold">
                                        <a href="{{ route('backend.geodivisions.show', ['geoDivision' => $division->uuid, 'slug' => $division->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-nowrap">
                                            {{ $division->active_translation?->name ?? '—' }}
                                        </a>
                                    </td>

                                    <td>
                                        @if ($division->type)
                                            <a href="{{ route('backend.geodivisiontypes.show', ['geoDivisionType' => $division->type->uuid, 'slug' => $division->type->active_translation?->slug ?? $division->type->code]) }}" class="badge badge-soft-primary text-decoration-none">
                                                {{ $division->type->active_translation?->name ?? $division->type->code }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td class="text-muted">
                                        {{ $division->level }}
                                    </td>

                                    <td>
                                        @if ($division->parent)
                                            <a href="{{ route('backend.geodivisions.show', ['geoDivision' => $division->parent->uuid, 'slug' => $division->parent->active_translation?->slug ?? 'null']) }}" class="text-decoration-none">
                                                {{ $division->parent->active_translation?->name ?? '—' }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.aucunParent') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($division->country)
                                            <a href="{{ route('backend.geocountries.show', ['geoCountry' => $division->country->uuid, 'slug' => $division->country->active_translation?->slug ?? $division->country->code]) }}" class="text-decoration-none">
                                                {{ $division->country->active_translation?->name ?? $division->country->code }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="{{ $division->status?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $division->status?->label() ?? __('v1/backend/views/pages/geos/divisions/partials/_index-table.inconnu') }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.geodivisions.audits', ['geoDivision' => $division->uuid, 'slug' => $division->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i>{{ $division->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.geodivisions.activity-logs', ['geoDivision' => $division->uuid, 'slug' => $division->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-pulse me-1"></i>{{ $division->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($division->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.geodivisions.trashed'))
                                        <td class="text-muted">
                                            @formatDate($division->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($division->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <form action="{{ route('backend.geodivisions.restore', ['uuid' => $division->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $division->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.restaurer') }}" onclick="confirmAction('restore-form-{{ $division->uuid }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.restaurerCetteDivision') }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.restaurer') }}', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.geodivisions.force-destroy', ['uuid' => $division->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $division->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.supprimerDefinitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $division->uuid }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.confirmerSuppressionDefinitive') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        @endif

                                        @if (!$division->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <a href="{{ route('backend.geodivisions.show', ['geoDivision' => $division->uuid, 'slug' => $division->active_translation?->slug ?? 'null']) }}"
                                                   class="btn btn-sm btn-light" title="{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.geodivisions.edit', ['geoDivision' => $division->uuid]) }}"
                                                   class="btn btn-sm btn-soft-primary" title="{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($division->status === \App\Enums\Geos\Divisions\GeoDivisionStatus::Active)
                                                    <form action="{{ route('backend.geodivisions.deactivate', ['geoDivision' => $division->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $division->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" onclick="confirmAction('deactivate-form-{{ $division->uuid }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.desactiverCetteDivision') }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.desactiver') }}', 'warning')" title="{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.desactiver') }}">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.geodivisions.activate', ['geoDivision' => $division->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $division->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('activate-form-{{ $division->uuid }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.activerCetteDivision') }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.activer') }}', 'success')" title="{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.activer') }}">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.geodivisions.destroy', ['geoDivision' => $division->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $division->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $division->uuid }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.supprimerCetteDivision') }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.supprimer') }}', 'warning')" title="{{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.geodivisions.trashed') ? 12 : 11 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        {{ __('v1/backend/views/pages/geos/divisions/partials/_index-table.aucuneDivisionTrouvee') }}
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
            $('#divisions-table').DataTable({
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
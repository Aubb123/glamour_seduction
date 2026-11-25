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
            'label' => __('v1/backend/views/pages/geos/division-types/partials/_index-table.typeDeDivision'),
            'value' => $divisionTypes->count(),
            'icon'  => 'bx bx-map',
            'color' => 'primary',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/division-types/partials/_index-table.actifs'),
            'value' => $divisionTypes->where('status', \App\Enums\Geos\DivisionTypes\GeoDivisionTypeStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/division-types/partials/_index-table.inactifs'),
            'value' => $divisionTypes->where('status', \App\Enums\Geos\DivisionTypes\GeoDivisionTypeStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/division-types/partials/_index-table.audits'),
            'value' => $divisionTypes->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => __('v1/backend/views/pages/geos/division-types/partials/_index-table.activites'),
            'value' => $divisionTypes->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.listeDesTypes') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.actions') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('v1/backend/views/pages/geos/division-types/partials/_index-table.listeDesTypes'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.geodivisiontypes.index'],
                                ['label' => __('v1/backend/views/pages/geos/division-types/partials/_index-table.corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.geodivisiontypes.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="division-types-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.code') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.niveau') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.libelle') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.ordre') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.statut') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.audits') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.logs') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.creeLe') }}</th>
                                @if(request()->routeIs('backend.geodivisiontypes.trashed'))
                                    <th scope="col">{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.supprimeLe') }}</th>
                                @endif
                                <th scope="col">{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($divisionTypes as $divisionType)
                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-primary">{{ $divisionType->code }}</span>
                                    </td>

                                    <td class="text-muted">
                                        {{ $divisionType->level }}
                                    </td>

                                    <td class="fw-bold">
                                        {{ $divisionType->active_translation?->name ?? '—' }}
                                    </td>

                                    <td class="text-muted">
                                        {{ $divisionType->sort_order }}
                                    </td>

                                    <td>
                                        <span class="{{ $divisionType->status?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $divisionType->status?->label() ?? __('v1/backend/views/pages/geos/division-types/partials/_index-table.inconnu') }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.geodivisiontypes.audits', ['geoDivisionType' => $divisionType->uuid]) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i>{{ $divisionType->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.geodivisiontypes.activity-logs', ['geoDivisionType' => $divisionType->uuid]) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-pulse me-1"></i>{{ $divisionType->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($divisionType->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.geodivisiontypes.trashed'))
                                        <td class="text-muted">
                                            @formatDate($divisionType->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($divisionType->trashed())
                                            <div class="hstack gap-2 fs_15">

                                                <form action="{{ route('backend.geodivisiontypes.restore', ['uuid' => $divisionType->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $divisionType->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.restaurer') }}" onclick="confirmAction('restore-form-{{ $divisionType->uuid }}', '{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.restaurerCeType') }}', '{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.restaurer') }}', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.geodivisiontypes.force-destroy', ['uuid' => $divisionType->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $divisionType->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.supprimerDefinitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $divisionType->uuid }}', '{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.confirmerSuppressionDefinitive') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        @endif

                                        @if (!$divisionType->trashed())
                                            <div class="hstack gap-2 fs_15">

                                                <a href="{{ route('backend.geodivisiontypes.show', ['geoDivisionType' => $divisionType->uuid, 'slug' => $divisionType->active_translation?->slug ?? $divisionType->code]) }}"
                                                   class="btn btn-sm btn-light" title="{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.geodivisiontypes.edit', ['geoDivisionType' => $divisionType->uuid]) }}"
                                                   class="btn btn-sm btn-soft-primary" title="{{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.geodivisiontypes.trashed') ? 10 : 9 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        {{ __('v1/backend/views/pages/geos/division-types/partials/_index-table.aucunTypeTrouve') }}
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
            $('#division-types-table').DataTable({
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


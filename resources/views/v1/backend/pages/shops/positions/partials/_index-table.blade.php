@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset(getEnvFolder() . 'others/backend/datatables/buttons.dataTables.min.css') }}">
    <style>
        .dataTables_paginate .paginate_button { display: inline-flex !important; align-items: center !important; justify-content: center !important; }
    </style>
@endpush

@include('v1.backend.layouts.partials._statistics', [
    'statistics' => [
        ['label' => __('Postes'), 'value' => $positions->count(), 'icon' => 'bx bx-briefcase', 'color' => 'primary'],
        ['label' => __('Actifs'), 'value' => $positions->where('status', \App\Enums\Shops\Positions\PositionStatus::Active)->count(), 'icon' => 'bx bx-check-circle', 'color' => 'success'],
        ['label' => __('Inactifs'), 'value' => $positions->where('status', \App\Enums\Shops\Positions\PositionStatus::Inactive)->count(), 'icon' => 'bx bx-x-circle', 'color' => 'danger'],
        ['label' => __('Audits'), 'value' => $positions->sum('audits_count'), 'icon' => 'bx bx-file', 'color' => 'secondary'],
        ['label' => __("Logs d'activité"), 'value' => $positions->sum('activity_logs_count'), 'icon' => 'bx bx-pulse', 'color' => 'warning'],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Liste des postes') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{ __('Actions') }}</button>
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Nouveau poste'), 'icon' => 'bx bx-plus', 'route' => 'backend.positions.create'],
                                ['label' => __('Liste des postes'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.positions.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.positions.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="positions-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Code') }}</th>
                                <th scope="col">{{ __('Portée') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Personnel') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if (request()->routeIs('backend.positions.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col" class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($positions as $position)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('backend.positions.show', ['position' => $position->uuid, 'slug' => $position->active_translation?->slug ?? 'null']) }}" class="fw-semibold text-decoration-none text-nowrap">
                                            {{ $position->active_translation?->name ?? __('Sans nom') }}
                                        </a>
                                    </td>
                                    <td><span class="fw-semibold"><code>{{ $position->code }}</code></span></td>
                                    <td><span class="{{ $position->context?->badgeClass() }}">{{ $position->context?->label() }}</span></td>
                                    <td><span class="{{ $position->status?->badgeClass() }}">{{ $position->status?->label() }}</span></td>
                                    <td><span class="badge badge-soft-primary">{{ ($position->shop_users_count ?? 0) + ($position->warehouse_users_count ?? 0) }}</span></td>
                                    <td class="text-muted">@formatDate($position->created_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                    @if (request()->routeIs('backend.positions.trashed'))
                                        <td class="text-muted">@formatDate($position->deleted_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                    @endif
                                    <td class="text-center">
                                        @if ($position->trashed())
                                            <div class="hstack gap-2 fs-15 justify-content-center">
                                                <form action="{{ route('backend.positions.restore', ['uuid' => $position->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $position->uuid }}">
                                                    @csrf @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $position->uuid }}', '{{ __('Restaurer ce poste ?') }}', '{{ __('Restaurer') }}', 'success')"><i class="bx bx-undo"></i></button>
                                                </form>
                                                <form action="{{ route('backend.positions.force_destroy', ['uuid' => $position->uuid]) }}" method="POST" style="display:inline-block" id="force-delete-form-{{ $position->uuid }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" onclick="confirmAction('force-delete-form-{{ $position->uuid }}', '{{ __('Supprimer définitivement ce poste ?') }}', '{{ __('Supprimer définitivement') }}', 'danger')"><i class="bx bx-trash"></i></button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="hstack gap-2 fs-15 justify-content-center">
                                                <a href="{{ route('backend.positions.show', ['position' => $position->uuid, 'slug' => $position->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-show"></i></a>
                                                <a href="{{ route('backend.positions.edit', ['position' => $position->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}"><i class="bx bx-edit"></i></a>
                                                @if ($position->status === \App\Enums\Shops\Positions\PositionStatus::Active)
                                                    <form action="{{ route('backend.positions.deactivate', ['position' => $position->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $position->uuid }}">
                                                        @csrf @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" title="{{ __('Désactiver') }}" onclick="confirmAction('deactivate-form-{{ $position->uuid }}', '{{ __('Désactiver ce poste ?') }}', '{{ __('Désactiver') }}', 'warning')"><i class="bx bx-toggle-left"></i></button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.positions.activate', ['position' => $position->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $position->uuid }}">
                                                        @csrf @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Activer') }}" onclick="confirmAction('activate-form-{{ $position->uuid }}', '{{ __('Activer ce poste ?') }}', '{{ __('Activer') }}', 'success')"><i class="bx bx-toggle-right"></i></button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('backend.positions.destroy', ['position' => $position->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $position->uuid }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer') }}" onclick="confirmAction('delete-form-{{ $position->uuid }}', '{{ __('Supprimer ce poste ?') }}', '{{ __('Supprimer') }}', 'warning')"><i class="bx bx-trash"></i></button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.positions.trashed') ? 9 : 8 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-briefcase fs-3 d-block mb-2"></i> {{ __('Aucun poste trouvé.') }}
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
            $('#positions-table').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>' + 'tr' + '<"row mt-3"<"col-md-5"l><"col-md-7"p>>',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language: { url: "{{ app()->getLocale() === 'fr' ? asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/fr-FR.json') : asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/en-GB.json') }}" },
            });
        });
    </script>
@endpush

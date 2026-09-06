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
            'label' => __('Abonnés'),
            'value' => $subscribes->count(),
            'icon'  => 'bx bx-send',
            'color' => 'primary',
        ],
        [
            'label' => __('Actifs'),
            'value' => $subscribes->where('status', \App\Enums\Subscribes\SubscribeStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Inactifs'),
            'value' => $subscribes->where('status', \App\Enums\Subscribes\SubscribeStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Vérifiés'),
            'value' => $subscribes->whereNotNull('verified_at')->count(),
            'icon'  => 'bx bx-check',
            'color' => 'info',
        ],
        [
            'label' => __('Non vérifiés'),
            'value' => $subscribes->whereNull('verified_at')->count(),
            'icon'  => 'bx bx-time-five',
            'color' => 'warning',
        ],
        [
            'label' => __('Activités'),
            'value' => $subscribes->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Abonnés') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Liste des abonnés'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.subscribes.index'],
                                ['label' => __('Historique'), 'icon' => 'bx bx-time-five', 'route' => 'backend.subscribe-histories.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.subscribes.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="subscribes-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Email') }}</th>
                                <th scope="col">{{ __('Utilisateur') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Vérifié') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs d\'activité') }}</th>
                                <th scope="col">{{ __('Inscrit le') }}</th>
                                @if(request()->routeIs('backend.subscribes.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subscribes as $subscribe)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold"><code>{{ $subscribe->email }}</code></td>
                                    <td>
                                        @if($subscribe->user)
                                            <a href="{{ route('backend.users.show', ['user' => $subscribe->user->uuid, 'slug' => $subscribe->user->slug]) }}"
                                                class="fw-semibold text-primary text-decoration-none">
                                                {{ $subscribe->user->first_name . ' ' . $subscribe->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="{{ $subscribe->status?->badgeClass() ?? 'badge bg-secondary-subtle text-secondary' }}">
                                            {{ $subscribe->status?->label() ?? __('Inconnu') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($subscribe->verified_at)
                                            <span class="badge bg-info-subtle text-info">{{ __('Oui') }}</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">{{ __('Non') }}</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.subscribes.audits', ['subscribe' => $subscribe->uuid]) }}" class="text-decoration-none">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-file me-1"></i>
                                                {{ $subscribe->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.subscribes.activity-logs', ['subscribe' => $subscribe->uuid]) }}" class="text-decoration-none">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-pulse me-1"></i>
                                                {{ $subscribe->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">@formatDate($subscribe->created_at, 'D MMMM YYYY - HH:mm:ss')</td>

                                    @if(request()->routeIs('backend.subscribes.trashed'))
                                        <td class="text-muted">@formatDate($subscribe->deleted_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                    @endif

                                    <td>
                                        @if ($subscribe->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.subscribes.restore', ['uuid' => $subscribe->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $subscribe->uuid }}">
                                                    @csrf @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $subscribe->uuid }}', '{{ __('Restaurer cet abonné ?') }}', 'Restaurer', 'success')"><i class="bx bx-undo"></i></button>
                                                </form>
                                                <form action="{{ route('backend.subscribes.force_destroy', ['uuid' => $subscribe->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $subscribe->uuid }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $subscribe->uuid }}', '{{ __('Supprimer définitivement cet abonné ? Cette action est irréversible.') }}')"><i class="bx bx-trash"></i></button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.subscribes.show', ['subscribe' => $subscribe->uuid]) }}" class="btn btn-sm btn-info" title="{{ __('Voir') }}"><i class="bx bx-show"></i></a>

                                                @if (!$subscribe->status || $subscribe->status === \App\Enums\Subscribes\SubscribeStatus::Inactive)
                                                    <form action="{{ route('backend.subscribes.activate', ['subscribe' => $subscribe->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $subscribe->uuid }}">
                                                        @csrf @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-success" onclick="confirmAction('activate-form-{{ $subscribe->uuid }}', '{{ __('Activer cet abonné ?') }}', 'Activer', 'success')" title="{{ __('Activer') }}"><i class="bx bx-check"></i></button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.subscribes.deactivate', ['subscribe' => $subscribe->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $subscribe->uuid }}">
                                                        @csrf @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-warning" onclick="confirmAction('deactivate-form-{{ $subscribe->uuid }}', '{{ __('Désactiver cet abonné ?') }}', 'Désactiver', 'warning')" title="{{ __('Désactiver') }}"><i class="bx bx-x"></i></button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.subscribes.destroy', ['subscribe' => $subscribe->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $subscribe->uuid }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction('delete-form-{{ $subscribe->uuid }}', '{{ __('Supprimer cet abonné ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}"><i class="bx bx-trash"></i></button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.subscribes.trashed') ? 8 : 7 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucun abonné trouvé.') }}
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
            $('#subscribes-table').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>tr<"row mt-3"<"col-md-5"l><"col-md-7"p>>',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    url: "{{ app()->getLocale() === 'fr' ? asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/fr-FR.json') : asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/en-GB.json') }}"
                },
            });
        });
    </script>
@endpush
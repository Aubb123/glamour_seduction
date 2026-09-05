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
            'label' => __('Plateformes'),
            'value' => $platforms->count(),
            'icon'  => 'bx bx-server',
            'color' => 'primary',
        ],
        [
            'label' => __('Actives'),
            'value' => $platforms->where('status', \App\Enums\Clouds\Platforms\CloudPlatformStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Inactives'),
            'value' => $platforms->where('status', \App\Enums\Clouds\Platforms\CloudPlatformStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Dossiers'),
            'value' => $platforms->sum('folders_count'),
            'icon'  => 'bx bx-folder',
            'color' => 'info',
        ],
        [
            'label' => __('Fichiers'),
            'value' => $platforms->sum('files_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => __('Activités'),
            'value' => $platforms->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Plateformes Cloud') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Ajouter une plateforme'), 'icon' => 'bx bx-plus', 'route' => 'backend.cloud-platforms.create'],
                                ['label' => __('Liste des plateformes'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.cloud-platforms.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.cloud-platforms.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="cloud-platforms-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Label') }}</th>
                                <th scope="col">{{ __('Endpoint') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Dossiers') }}</th>
                                <th scope="col">{{ __('Fichiers') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs d\'activité') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if(request()->routeIs('backend.cloud-platforms.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($platforms as $platform)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        <code>{{ $platform->name }}</code>
                                    </td>

                                    <td>{{ $platform->label }}</td>

                                    <td>
                                        <code class="text-muted small">{{ Str::limit($platform->api_endpoint, 40) ?: '—' }}</code>
                                    </td>

                                    <td>
                                        <span class="{{ $platform->status?->badgeClass() ?? 'badge bg-soft-secondary' }}">
                                            {{ $platform->status?->label() ?? __('Inconnu') }}
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.cloud-platforms.folders', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]) }}">
                                            <span class="badge bg-primary">{{ $platform->folders_count ?? 0 }}</span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.cloud-platforms.files', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]) }}">
                                            <span class="badge bg-primary">{{ $platform->files_count ?? 0 }}</span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.cloud-platforms.audits', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]) }}">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-file me-1"></i> {{ $platform->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.cloud-platforms.activity-logs', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]) }}">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-pulse me-1"></i>
                                                {{ $platform->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($platform->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.cloud-platforms.trashed'))
                                        <td class="text-muted">
                                            @formatDate($platform->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($platform->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.cloud-platforms.restore', ['uuid' => $platform->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $platform->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $platform->uuid }}', '{{ __('Restaurer cette plateforme ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.cloud-platforms.force_destroy', ['uuid' => $platform->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $platform->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $platform->uuid }}', '{{ __('Supprimer définitivement cette plateforme ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$platform->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.cloud-platforms.show', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]) }}" class="btn btn-sm btn-info" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.cloud-platforms.edit', ['cloudPlatform' => $platform->uuid]) }}" class="btn btn-sm btn-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($platform->status === \App\Enums\Clouds\Platforms\CloudPlatformStatus::Active)
                                                    <form action="{{ route('backend.cloud-platforms.toggle_active', ['cloudPlatform' => $platform->uuid]) }}" method="POST" id="deactivate-form-{{ $platform->uuid }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-warning" onclick="confirmAction('deactivate-form-{{ $platform->uuid }}', '{{ __('Désactiver cette plateforme ?') }}', 'Désactiver', 'warning')" title="{{ __('Désactiver') }}">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.cloud-platforms.toggle_active', ['cloudPlatform' => $platform->uuid]) }}" method="POST" id="activate-form-{{ $platform->uuid }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-success" onclick="confirmAction('activate-form-{{ $platform->uuid }}', '{{ __('Activer cette plateforme ?') }}', 'Activer', 'success')" title="{{ __('Activer') }}">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.cloud-platforms.destroy', ['cloudPlatform' => $platform->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $platform->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction('delete-form-{{ $platform->uuid }}', '{{ __('Supprimer cette plateforme ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.cloud-platforms.trashed') ? 8 : 7 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucune plateforme cloud trouvée.') }}
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
            $('#cloud-platforms-table').DataTable({
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
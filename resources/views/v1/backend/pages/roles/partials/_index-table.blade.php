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
            'label' => __('Rôles'),
            'value' => $roles->count(),
            'icon'  => 'bx bx-shield-quarter',
            'color' => 'primary',
        ],
        [
            'label' => __('Actifs'),
            'value' => $roles->where('status', \App\Enums\Roles\RoleStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Inactifs'),
            'value' => $roles->where('status', \App\Enums\Roles\RoleStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Permissions'),
            'value' => $roles->sum('permissions_count'),
            'icon'  => 'bx bx-lock',
            'color' => 'info',
        ],
        [
            'label' => __('Utilisateurs'),
            'value' => $roles->sum('users_count'),
            'icon'  => 'bx bx-user',
            'color' => 'warning',
        ],
        [
            'label' => __('Activités'),
            'value' => $roles->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Rôles') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Liste des rôles', 'icon' => 'bx bx-list-ul', 'route' => 'backend.roles.index'],
                                ['label' => 'Corbeille', 'icon' => 'bx bx-trash', 'route' => 'backend.roles.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="roles-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <!-- <th scope="col">{{ __('Nom interne') }}</th> -->
                                <th scope="col">{{ __('Guard') }}</th>
                                <th scope="col">{{ __('Actif') }}</th>
                                <th scope="col">{{ __('Description') }}</th>
                                <th scope="col">{{ __('Permissions') }}</th>
                                <th scope="col">{{ __('Utilisateurs') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs d\'activité') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                <th scope="col">{{ __('Modifié le') }}</th>
                                @if(request()->routeIs('backend.roles.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="fw-bold">
                                        {{ $role->active_translation?->label ?? '—' }}
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-info">{{ $role->guard_name }}</span>
                                    </td>

                                    <!-- 
                                    Pas besoin d'afficher le nom interne sur le dashboard pour les gérants (admins, moderators, etc.), car il est principalement utilisé pour la gestion interne et les autorisations.
                                    <td>
                                        <code class="text-muted">{{ $role->name ?? '—' }}</code>
                                    </td> 
                                    -->

                                    <td>
                                        <span class="{{ $role->status?->badgeClass() ?? 'badge bg-soft-secondary' }}">
                                            {{ $role->status?->label() ?? __('Inconnu') }}
                                        </span>
                                    </td>

                                    <td>
                                        <span title="{{ $role->active_translation?->description ?? '—' }}">
                                            {{ Str::limit($role->active_translation?->description ?? '—', 30) }}
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <a href="{{ route('backend.roles.permissions', ['role' => $role->uuid, 'slug' => $role->active_translation->slug ?? 'undefined']) }}" class="text-decoration-none">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-shield-quarter me-1"></i> {{ $role->permissions()->count() }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.roles.users', ['role' => $role->uuid, 'slug' => $role->active_translation->slug ?? 'undefined']) }}" class="text-decoration-none">
                                            <span class="badge bg-outline-primary">
                                                <i class="bx bx-user me-1"></i> {{ $role->users()->count() }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.roles.audits', ['role' => $role->uuid, 'slug' => $role->active_translation->slug ?? 'undefined']) }}" class="text-decoration-none">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-file me-1"></i> {{ $role->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.roles.activity-logs', ['role' => $role->uuid, 'slug' => $role->active_translation->slug ?? 'undefined']) }}" class="text-decoration-none">
                                            <span class="badge bg-outline-primary">
                                                <i class="bx bx-pulse me-1"></i> {{ $role->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($role->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($role->updated_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.roles.trashed'))
                                        <td class="text-muted">
                                            @formatDate($role->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($role->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.roles.restore', ['uuid' => $role->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $role->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $role->uuid }}', '{{ __('Restaurer ce rôle ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.roles.force_destroy', ['uuid' => $role->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{$role->uuid}}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $role->uuid }}', '{{ __('Supprimer définitivement ce rôle ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$role->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.roles.show', ['role' => $role->uuid, 'slug' => $role->active_translation->slug ?? 'not-found']) }}" class="btn btn-sm btn-info" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.roles.edit', ['role' => $role->uuid]) }}" class="btn btn-sm btn-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucun rôle trouvé.') }}
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
            $('#roles-table').DataTable({
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
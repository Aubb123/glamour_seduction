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
            'label' => __('Utilisateurs'),
            'value' => $users->count(),
            'icon'  => 'bx bx-user',
            'color' => 'primary',
        ],
        [
            'label' => __('Actifs'),
            'value' => $users->where('status', \App\Enums\Users\UserStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Suspendus'),
            'value' => $users->where('status', \App\Enums\Users\UserStatus::Suspended)->count(),
            'icon'  => 'bx bx-pause',
            'color' => 'warning',
        ],
        [
            'label' => __('Bannis'),
            'value' => $users->where('status', \App\Enums\Users\UserStatus::Banned)->count(),
            'icon'  => 'bx bx-minus',
            'color' => 'danger',
        ],
        [
            'label' => __('Abonnements'),
            'value' => $users->sum('subscribes_count'),
            'icon'  => 'bx bx-send',
            'color' => 'info',
        ],
        [
            'label' => __('Activités'),
            'value' => $users->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Utilisateurs</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Liste des utilisateurs', 'icon' => 'bx bx-list-ul', 'route' => 'backend.users.index'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="users-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Photo') }}</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Email') }}</th>
                                <th scope="col">{{ __('Pays') }}</th>
                                <th scope="col">{{ __('Rôles') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Abonnements') }}</th>
                                <th scope="col">{{ __('Galeries') }}</th>
                                <th scope="col">{{ __('Blogs') }}</th>
                                <th scope="col">{{ __('Commentaires') }}</th>
                                <th scope="col">{{ __('Réponses') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs d\'activité') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)

                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <a href="{{ $user->getFirstImage()['url_img'] }}" ttarget="_blank" rrel="noopener noreferrer">
                                            <img src="{{ $user->getFirstImage()['url_img'] }}" alt="{{ __('Photo') }}" style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%; border: 2px solid #dee2e6;">
                                        </a>
                                    </td>

                                    <td class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</td>
                                    
                                    <td>{{ $user->email }}</td>
                                    
                                    <td>
                                        <span class="badge bg-outline-secondary">
                                            <a href="{{ $user->country ? route('backend.geocountries.show', ['geoCountry' => $user->country->uuid, 'slug' => $user->country->active_translation?->slug ?? 'null']) : '#' }}">
                                                {{ $user->country?->active_translation?->name ?? 'null' }}
                                            </a>
                                        </span>
                                    </td>

                                    <td>
                                        @forelse ($user->roles as $role) 
                                            @if($loop->iteration <= 2)
                                                <a href="{{ route('backend.roles.show', ['role' => $role->uuid, 'slug' => $role?->active_translation?->slug ?? 'null']) }}">
                                                    <span class="badge bg-info me-1">{{ $role->active_translation?->label ?? 'null' }}</span>
                                                </a>
                                            @else
                                                <span class="badge bg-primary me-1">...</span>
                                            @endif
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </td>

                                    <td>
                                        @if ($user->status === \App\Enums\Users\UserStatus::Active)
                                            <span class="badge bg-soft-success">{{ __('Actif') }}</span>
                                        @elseif ($user->status === \App\Enums\Users\UserStatus::Suspended)
                                            <span class="badge bg-soft-warning">{{ __('Suspendu') }}</span>
                                        @elseif ($user->status === \App\Enums\Users\UserStatus::Banned)
                                            <span class="badge bg-soft-danger">{{ __('Banni') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                            <a href="{{ route('backend.users.subscribes', ['user' => $user->uuid, 'slug' => $user->slug]) }}" class="text-decoration-none">
                                            <span class="badge bg-info">
                                                <i class="bx bx-send me-1"></i> {{ $user->subscribes_count }}
                                            </span>
                                        </a>
                                    </td>
                                    
                                    <td>
                                        <a href="{{ route('backend.users.galleries', ['user' => $user->uuid, 'slug' => $user->slug]) }}" class="text-decoration-none">
                                            <span class="badge bg-success">
                                                <i class="bx bx-image me-1"></i> {{ $user->galleries_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.users.blog-posts', ['user' => $user->uuid, 'slug' => $user->slug]) }}"
                                           class="text-decoration-none">
                                            <span class="badge bg-secondary">
                                                <i class="bx bx-book me-1"></i> {{ $user->blog_posts_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.users.blog-post-comments', ['user' => $user->uuid, 'slug' => $user->slug]) }}"
                                           class="text-decoration-none">
                                            <span class="badge bg-warning">
                                                <i class="bx bx-message-square me-1"></i> {{ $user->blog_post_comments_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.users.blog-post-comment-replies', ['user' => $user->uuid, 'slug' => $user->slug]) }}"
                                           class="text-decoration-none">
                                            <span class="badge bg-danger">
                                                <i class="bx bx-message-rounded me-1"></i> {{ $user->blog_post_comment_replies_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.users.audits', ['user' => $user->uuid, 'slug' => $user->slug]) }}" class="text-decoration-none">
                                            <span class="badge bg-info">
                                                <i class="bx bx-file me-1"></i> {{ $user->audits_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.users.activity-logs', ['user' => $user->uuid, 'slug' => $user->slug]) }}" class="text-decoration-none">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-pulse me-1"></i> {{ $user->activity_logs_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">@formatDate($user->created_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                    
                                    <td>
                                        <div class="hstack gap-2 fs-15">
                                            <a href="{{ route('backend.users.show', ['user' => $user->uuid, 'slug' => $user->slug]) }}" class="btn btn-sm btn-info" title="{{ __('Voir') }}">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="{{ route('backend.users.edit', ['user' => $user->uuid]) }}" class="btn btn-sm btn-primary" title="{{ __('Modifier') }}">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            @if ($user->status === \App\Enums\Users\UserStatus::Active)
                                                <form action="{{ route('backend.users.suspend', ['user' => $user->uuid]) }}" method="POST" class="d-inline" id="suspend-form-{{ $user->uuid }}">
                                                    @csrf @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-secondary" title="{{ __('Suspendre') }}" onclick="confirmAction('suspend-form-{{ $user->uuid }}', '{{ __('Suspendre cet utilisateur ?') }}', 'Suspendre', 'warning')">
                                                        <i class="bx bx-pause"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('backend.users.ban', ['user' => $user->uuid]) }}" method="POST" class="d-inline" id="ban-form-{{ $user->uuid }}">
                                                    @csrf @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('Bannir') }}" onclick="confirmAction('ban-form-{{ $user->uuid }}', '{{ __('Bannir cet utilisateur ?') }}', 'Bannir', 'danger')">
                                                        <i class="bx bx-minus"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('backend.users.activate', ['user' => $user->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $user->uuid }}">
                                                    @csrf @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('Activer') }}" onclick="confirmAction('activate-form-{{ $user->uuid }}', '{{ __('Activer cet utilisateur ?') }}', 'Activer', 'success')">
                                                        <i class="bx bx-toggle-right"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucun utilisateur trouvé.') }}
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
            $('#users-table').DataTable({
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
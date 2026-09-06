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
            'label' => __('Réponses'),
            'value' => $replies->count(),
            'icon'  => 'bx bx-message-rounded',
            'color' => 'primary',
        ],
        [
            'label' => __('Approuvées'),
            'value' => $replies->where('is_visible', true)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('En attente'),
            'value' => $replies->where('is_visible', false)->count(),
            'icon'  => 'bx bx-time-five',
            'color' => 'warning',
        ],
        [
            'label' => __('Audits'),
            'value' => $replies->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'info',
        ],
        [
            'label' => __('Activités'),
            'value' => $replies->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'danger',
        ],
        [
            'label' => __('Auteurs'),
            'value' => $replies->pluck('user_id')->filter()->unique()->count(),
            'icon'  => 'bx bx-user',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Réponses aux commentaires</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Liste des réponses', 'icon' => 'bx bx-list-ul', 'route' => 'backend.blog-post-comment-replies.index'],
                                ['label' => 'Corbeille', 'icon' => 'bx bx-trash', 'route' => 'backend.blog-post-comment-replies.trashed'],
                            ]
                        ])
                    </ul>

                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="blog-comment-replies-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Commentaire') }}</th>
                                <th scope="col">{{ __('Auteur') }}</th>
                                <th scope="col">{{ __('Contenu') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs') }}</th>
                                <th scope="col">{{ __('Visible') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if(request()->routeIs('backend.blog-post-comment-replies.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($replies as $reply)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        @if($reply->comment)
                                            <a href="{{ route('backend.blog-post-comments.show', ['blogPostComment' => $reply->comment->uuid]) }}"
                                               class="text-primary text-decoration-none">
                                                {{ Str::limit($reply->comment?->content ?? '—', 50) }}
                                            </a>
                                        @else
                                            <code>—</code>
                                        @endif
                                    </td>

                                    <td>
                                        @if($reply->user)
                                            <a href="{{ route('backend.users.show', ['user' => $reply->user->uuid, 'slug' => $reply->user->slug]) }}"
                                               class="fw-semibold text-primary text-decoration-none">
                                                {{ $reply->user->first_name . ' ' . $reply->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('Anonyme') }}</span>
                                        @endif
                                    </td>

                                    <td class="text-muted" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ Str::limit($reply->content ?? '—', 80) }}
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.blog-post-comment-replies.audits', ['blogPostCommentReply' => $reply->uuid]) }}"
                                           class="text-decoration-none">
                                            <span class="badge bg-primary-subtle text-primary">
                                                <i class="bx bx-file me-1"></i> {{ $reply->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.blog-post-comment-replies.activity-logs', ['blogPostCommentReply' => $reply->uuid]) }}"
                                           class="text-decoration-none">
                                            <span class="badge bg-info-subtle text-info">
                                                <i class="bx bx-pulse me-1"></i> {{ $reply->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        @if ($reply->is_visible)
                                            <span class="badge bg-success-subtle text-success">{{ __('Approuvé') }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">{{ __('En attente') }}</span>
                                        @endif
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($reply->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.blog-post-comment-replies.trashed'))
                                        <td class="text-muted">
                                            @formatDate($reply->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($reply->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.blog-post-comment-replies.restore', ['uuid' => $reply->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $reply->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $reply->uuid }}', '{{ __('Restaurer cette réponse ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.blog-post-comment-replies.force_destroy', ['uuid' => $reply->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $reply->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $reply->uuid }}', '{{ __('Supprimer définitivement cette réponse ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$reply->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.blog-post-comment-replies.show', ['blogPostCommentReply' => $reply->uuid]) }}" class="btn btn-sm btn-info" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                @if ($reply->is_visible)
                                                    <form action="{{ route('backend.blog-post-comment-replies.deactivate', ['blogPostCommentReply' => $reply->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $reply->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-warning" onclick="confirmAction('deactivate-form-{{ $reply->uuid }}', '{{ __('Désapprouver cette réponse ?') }}', 'Désapprouver', 'warning')" title="{{ __('Désapprouver') }}">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.blog-post-comment-replies.activate', ['blogPostCommentReply' => $reply->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $reply->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-success" onclick="confirmAction('activate-form-{{ $reply->uuid }}', '{{ __('Approuver cette réponse ?') }}', 'Approuver', 'success')" title="{{ __('Approuver') }}">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.blog-post-comment-replies.destroy', ['blogPostCommentReply' => $reply->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $reply->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction('delete-form-{{ $reply->uuid }}', '{{ __('Supprimer cette réponse ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.blog-post-comment-replies.trashed') ? 10 : 9 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucune réponse trouvée.') }}
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
            $('#blog-comment-replies-table').DataTable({
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
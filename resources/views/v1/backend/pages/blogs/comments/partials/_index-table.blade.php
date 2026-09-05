

@include('v1.backend.layouts.partials._statistics', [
    'statistics' => [
        [
            'label' => __('Commentaires'),
            'value' => $comments->count(),
            'icon'  => 'bx bx-message-square',
            'color' => 'primary',
        ],
        [
            'label' => __('Approuvés'),
            'value' => $comments->where('is_visible', true)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('En attente'),
            'value' => $comments->where('is_visible', false)->count(),
            'icon'  => 'bx bx-time-five',
            'color' => 'warning',
        ],
        [
            'label' => __('Réponses'),
            'value' => $comments->sum('replies_count'),
            'icon'  => 'bx bx-corner-down-right',
            'color' => 'info',
        ],
        [
            'label' => __('Audits'),
            'value' => $comments->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'danger',
        ],
        [
            'label' => __('Activités'),
            'value' => $comments->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Commentaires</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Liste des commentaires', 'icon' => 'bx bx-list-ul', 'route' => 'backend.blog-post-comments.index'],
                                ['label' => 'Corbeille', 'icon' => 'bx bx-trash', 'route' => 'backend.blog-post-comments.trashed'],
                            ]
                        ])
                    </ul>

                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="blog-comments-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Article') }}</th>
                                <th scope="col">{{ __('Auteur') }}</th>
                                <th scope="col">{{ __('Contenu') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs') }}</th>
                                <th scope="col">{{ __('Réponses') }}</th>
                                <th scope="col">{{ __('Visible') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if(request()->routeIs('backend.blog-post-comments.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($comments as $comment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        @if($comment->post)
                                            <a href="{{ route('backend.blog-posts.show', ['blogPost' => $comment->post->uuid, 'slug' => $comment->post?->active_translation?->slug ?? 'null']) }}"
                                               class="text-primary text-decoration-none">
                                                {{ Str::limit($comment->post?->active_translation?->title ?? '-', 50) }}
                                            </a>
                                        @else
                                            <code>-</code>
                                        @endif
                                    </td>

                                    <td>
                                        @if($comment->user)
                                            <a href="{{ route('backend.users.show', ['user' => $comment->user->uuid, 'slug' => $comment->user->slug]) }}"
                                               class="fw-semibold text-primary text-decoration-none">
                                                {{ $comment->user->first_name . ' ' . $comment->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('Anonyme') }}</span>
                                        @endif
                                    </td>

                                    <td class="text-muted" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ Str::limit($comment->content ?? '—', 50) }}
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.blog-post-comments.audits', ['blogPostComment' => $comment->uuid]) }}"
                                           class="text-decoration-none">
                                            <span class="badge bg-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $comment->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.blog-post-comments.activity-logs', ['blogPostComment' => $comment->uuid]) }}"
                                           class="text-decoration-none">
                                            <span class="badge bg-soft-info">
                                                <i class="bx bx-pulse me-1"></i> {{ $comment->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.blog-post-comments.replies', ['blogPostComment' => $comment->uuid]) }}"
                                           class="text-decoration-none">
                                            <span class="badge bg-soft-success">
                                                <i class="bx bx-corner-down-right me-1"></i> {{ $comment->replies_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        @if ($comment->is_visible)
                                            <span class="badge bg-soft-success">{{ __('Approuvé') }}</span>
                                        @else
                                            <span class="badge bg-soft-danger">{{ __('En attente') }}</span>
                                        @endif
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($comment->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.blog-post-comments.trashed'))
                                        <td class="text-muted">
                                            @formatDate($comment->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($comment->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.blog-post-comments.restore', ['uuid' => $comment->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $comment->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $comment->uuid }}', '{{ __('Restaurer ce commentaire ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.blog-post-comments.force_destroy', ['uuid' => $comment->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $comment->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $comment->uuid }}', '{{ __('Supprimer définitivement ce commentaire ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$comment->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.blog-post-comments.show', ['blogPostComment' => $comment->uuid]) }}" class="btn btn-sm btn-info" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                @if ($comment->is_visible)
                                                    <form action="{{ route('backend.blog-post-comments.deactivate', ['blogPostComment' => $comment->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $comment->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-warning" onclick="confirmAction('deactivate-form-{{ $comment->uuid }}', '{{ __('Désapprouver ce commentaire ?') }}', 'Désapprouver', 'warning')" title="{{ __('Désapprouver') }}">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.blog-post-comments.activate', ['blogPostComment' => $comment->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $comment->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-success" onclick="confirmAction('activate-form-{{ $comment->uuid }}', '{{ __('Approuver ce commentaire ?') }}', 'Approuver', 'success')" title="{{ __('Approuver') }}">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.blog-post-comments.destroy', ['blogPostComment' => $comment->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $comment->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction('delete-form-{{ $comment->uuid }}', '{{ __('Supprimer ce commentaire ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.blog-post-comments.trashed') ? 11 : 10 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucun commentaire trouvé.') }}
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


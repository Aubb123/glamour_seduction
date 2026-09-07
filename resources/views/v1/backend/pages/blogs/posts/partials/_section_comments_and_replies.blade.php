<div class="tab-pane fade" id="tab_comments">
    <div class="row g-4">
        <div class="col-md-12">
            <div class="card border">
                <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold">
                        <i class="bx bx-message-square me-2 text-primary"></i>{{ __('Commentaires') }}
                        <span class="badge bg-primary ms-2">{{ $comments->count() }}</span>
                    </h6>
                    <a href="{{ route('backend.blog-post-comments.index') }}" class="btn btn-sm btn-soft-primary">
                        <i class="bx bx-link-external me-1"></i> {{ __('Voir tous les commentaires') }}
                    </a>
                </div>
                <div class="card-body">
                    @forelse ($comments as $comment)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>{{ $comment->user?->first_name . ' ' . $comment->user?->last_name ?? __('Anonyme') }}</strong>
                                    <small class="text-muted ms-2">@formatDate($comment->created_at, 'D MMMM YYYY - HH:mm:ss')</small>
                                </div>
                                <div class="hstack gap-1">
                                    <a href="{{ route('backend.blog-post-comments.show', ['blogPostComment' => $comment->uuid]) }}" class="btn btn-sm btn-light" title="{{ __('Détails') }}">
                                        <i class="bx bx-show"></i>
                                    </a>

                                    @if ($comment->is_visible)
                                        <form action="{{ route('backend.blog-post-comments.deactivate', ['blogPostComment' => $comment->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $comment->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="btn btn-sm btn-soft-warning" onclick="confirmAction('deactivate-form-{{ $comment->uuid }}', '{{ __('Désapprouver ce commentaire ?') }}', 'Désapprouver', 'warning')" title="{{ __('Désapprouver') }}">
                                                <i class="bx bx-toggle-left"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('backend.blog-post-comments.activate', ['blogPostComment' => $comment->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $comment->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('activate-form-{{ $comment->uuid }}', '{{ __('Approuver ce commentaire ?') }}', 'Approuver', 'success')" title="{{ __('Approuver') }}">
                                                <i class="bx bx-toggle-right"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('backend.blog-post-comments.destroy', ['blogPostComment' => $comment->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $comment->uuid }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $comment->uuid }}', '{{ __('Supprimer ce commentaire ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="p-3 bg-light rounded mb-2">
                                {!! nl2br(e($comment->content)) !!}
                            </div>

                            <div class="d-flex align-items-center gap-2 mb-2">
                                @if ($comment->is_visible)
                                    <span class="badge badge-soft-success">{{ __('Approuvé') }}</span>
                                @else
                                    <span class="badge badge-soft-danger">{{ __('En attente') }}</span>
                                @endif
                                <small class="text-muted">{{ $comment->replies->count() }} {{ __('réponse(s)') }}</small>
                            </div>

                            <!-- Réponses au commentaire -->
                            @if($comment->replies->isNotEmpty())
                                <div class="ms-4 ps-3 border-start border-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="bx bx-corner-down-right me-1"></i>{{ __('Réponses') }}
                                    </h6>
                                    @foreach ($comment->replies as $reply)
                                        <div class="border rounded p-2 mb-2 bg-light">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong class="small">{{ $reply->user?->first_name . ' ' . $reply->user?->last_name ?? __('Anonyme') }}</strong>
                                                    <small class="text-muted ms-2">@formatDate($reply->created_at, 'D MMMM YYYY - HH:mm:ss')</small>
                                                </div>
                                                <div class="hstack gap-1">
                                                    <a href="{{ route('backend.blog-post-comment-replies.show', ['blogPostCommentReply' => $reply->uuid]) }}" class="btn btn-sm btn-light" title="{{ __('Détails') }}">
                                                        <i class="bx bx-show"></i>
                                                    </a>

                                                    @if ($reply->is_visible)
                                                        <form action="{{ route('backend.blog-post-comment-replies.deactivate', ['blogPostCommentReply' => $reply->uuid]) }}" method="POST" class="d-inline" id="reply-deactivate-form-{{ $reply->uuid }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" class="btn btn-sm btn-soft-warning" onclick="confirmAction('reply-deactivate-form-{{ $reply->uuid }}', '{{ __('Désapprouver cette réponse ?') }}', 'Désapprouver', 'warning')" title="{{ __('Désapprouver') }}">
                                                                <i class="bx bx-toggle-left"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('backend.blog-post-comment-replies.activate', ['blogPostCommentReply' => $reply->uuid]) }}" method="POST" class="d-inline" id="reply-activate-form-{{ $reply->uuid }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('reply-activate-form-{{ $reply->uuid }}', '{{ __('Approuver cette réponse ?') }}', 'Approuver', 'success')" title="{{ __('Approuver') }}">
                                                                <i class="bx bx-toggle-right"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form action="{{ route('backend.blog-post-comment-replies.destroy', ['blogPostCommentReply' => $reply->uuid]) }}" method="POST" class="d-inline" id="reply-delete-form-{{ $reply->uuid }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('reply-delete-form-{{ $reply->uuid }}', '{{ __('Supprimer cette réponse ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="mt-1 small">
                                                {!! nl2br(e($reply->content)) !!}
                                            </div>
                                            <div class="mt-1">
                                                @if ($reply->is_visible)
                                                    <span class="badge badge-soft-success">{{ __('Approuvé') }}</span>
                                                @else
                                                    <span class="badge badge-soft-danger">{{ __('En attente') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    @empty
                        <p class="text-muted mb-0 text-center py-3">
                            <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                            {{ __('Aucun commentaire pour cet article.') }}
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
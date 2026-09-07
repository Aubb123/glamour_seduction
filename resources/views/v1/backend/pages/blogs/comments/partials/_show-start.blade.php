    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('Commentaire') }}
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.blog-post-comments.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                        </a>

                        <a href="{{ route('backend.blog-post-comments.audits', ['blogPostComment' => $comment->uuid]) }}" class="btn btn-soft-primary btn-sm">
                            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
                            <span class="badge bg-primary ms-1">{{ $comment->audits_count ?? 0 }}</span>
                        </a>

                        <a href="{{ route('backend.blog-post-comments.activity-logs', ['blogPostComment' => $comment->uuid]) }}" class="btn btn-soft-info btn-sm">
                            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
                            <span class="badge bg-primary ms-1">{{ $comment->activity_logs_count ?? 0 }}</span>
                        </a>

                        <a href="{{ route('backend.blog-post-comments.replies', ['blogPostComment' => $comment->uuid]) }}" class="btn btn-soft-success btn-sm">
                            <i class="bx bx-reply me-1"></i> {{ __('Réponses') }}
                            <span class="badge bg-primary ms-1">{{ $comment->replies_count ?? 0 }}</span>
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                @if ($comment->is_visible)
                                    <li>
                                        <form action="{{ route('backend.blog-post-comments.deactivate', ['blogPostComment' => $comment->uuid]) }}" method="POST" id="deactivate-form-{{ $comment->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $comment->uuid }}', '{{ __('Désapprouver ce commentaire ?') }}', 'Désapprouver', 'warning')">
                                                <i class="bx bx-toggle-left me-2 text-warning"></i> {{ __('Désapprouver') }}
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.blog-post-comments.activate', ['blogPostComment' => $comment->uuid]) }}" method="POST" id="activate-form-{{ $comment->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $comment->uuid }}', '{{ __('Approuver ce commentaire ?') }}', 'Approuver', 'success')">
                                                <i class="bx bx-toggle-right me-2 text-success"></i> {{ __('Approuver') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <form action="{{ route('backend.blog-post-comments.destroy', ['blogPostComment' => $comment->uuid]) }}" method="POST" id="delete-form-{{ $comment->uuid }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $comment->uuid }}', '{{ __('Supprimer ce commentaire ?') }}', 'Supprimer', 'warning')">
                                            <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                        </button>
                                    </form>
                                </li>

                            </ul>
                        </div>

                    </div>

                </div>

                <div class="card-body">

                    @include('v1.backend.pages.blogs.comments.partials._show-navigation', ['comment' => $comment])
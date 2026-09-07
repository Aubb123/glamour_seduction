    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('Réponse') }}
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.blog-post-comment-replies.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                        </a>

                        <a href="{{ route('backend.blog-post-comment-replies.audits', ['blogPostCommentReply' => $reply->uuid]) }}" class="btn btn-soft-primary btn-sm">
                            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
                            <span class="badge bg-primary ms-1">{{ $reply->audits_count ?? 0 }}</span>
                        </a>

                        <a href="{{ route('backend.blog-post-comment-replies.activity-logs', ['blogPostCommentReply' => $reply->uuid]) }}" class="btn btn-soft-info btn-sm">
                            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
                            <span class="badge bg-primary ms-1">{{ $reply->activity_logs_count ?? 0 }}</span>
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                @if ($reply->is_visible)
                                    <li>
                                        <form action="{{ route('backend.blog-post-comment-replies.deactivate', ['blogPostCommentReply' => $reply->uuid]) }}" method="POST" id="deactivate-form-{{ $reply->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $reply->uuid }}', '{{ __('Désapprouver cette réponse ?') }}', 'Désapprouver', 'warning')">
                                                <i class="bx bx-toggle-left me-2 text-warning"></i> {{ __('Désapprouver') }}
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.blog-post-comment-replies.activate', ['blogPostCommentReply' => $reply->uuid]) }}" method="POST" id="activate-form-{{ $reply->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $reply->uuid }}', '{{ __('Approuver cette réponse ?') }}', 'Approuver', 'success')">
                                                <i class="bx bx-toggle-right me-2 text-success"></i> {{ __('Approuver') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <form action="{{ route('backend.blog-post-comment-replies.destroy', ['blogPostCommentReply' => $reply->uuid]) }}" method="POST" id="delete-form-{{ $reply->uuid }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $reply->uuid }}', '{{ __('Supprimer cette réponse ?') }}', 'Supprimer', 'warning')">
                                            <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                        </button>
                                    </form>
                                </li>

                            </ul>
                        </div>

                    </div>

                </div>

                <div class="card-body">

                    @include('v1.backend.pages.blogs.replies.partials._show-navigation', ['reply' => $reply])
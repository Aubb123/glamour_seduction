<div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('Article') }} — <code>{{ Str::limit($post->active_translation?->title ?? '', 30) }}</code>
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.blog-posts.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                        </a>

                        <a href="{{ route('backend.blog-posts.audits', ['blogPost' => $post->uuid, 'slug' => $post->active_translation?->slug ?? 'null']) }}" class="btn btn-soft-primary btn-sm">
                            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
                            <span class="badge bg-primary ms-1">{{ $post->audits_count ?? 0 }}</span>
                        </a>

                        <a href="{{ route('backend.blog-posts.activity-logs', ['blogPost' => $post->uuid, 'slug' => $post->active_translation?->slug ?? 'null']) }}" class="btn btn-soft-info btn-sm">
                            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
                            <span class="badge bg-primary ms-1">{{ $post->activity_logs_count ?? 0 }}</span>
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                <li>
                                    <a href="{{ route('backend.blog-posts.edit', ['blogPost' => $post->uuid]) }}" class="dropdown-item">
                                        <i class="bx bx-edit me-2 text-primary"></i> {{ __('Modifier') }}
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                 @if ($post->status?->value === 'published')
                                    <li>
                                        <form action="{{ route('backend.blog-posts.change_status', ['blogPost' => $post->uuid]) }}" method="POST" id="draft-form-{{ $post->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="draft">
                                            <button type="button" class="dropdown-item" onclick="confirmAction('draft-form-{{ $post->uuid }}', '{{ __('Passer en brouillon cet article ?') }}', 'Brouillon', 'warning')">
                                                <i class="bx bx-hide me-2 text-warning"></i> {{ __('Passer en brouillon') }}
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.blog-posts.change_status', ['blogPost' => $post->uuid]) }}" method="POST" id="publish-form-{{ $post->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="published">
                                            <button type="button" class="dropdown-item" onclick="confirmAction('publish-form-{{ $post->uuid }}', '{{ __('Publier cet article ?') }}', 'Publier', 'success')">
                                                <i class="bx bx-show me-2 text-success"></i> {{ __('Publier') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li>
                                    <a href="#" class="dropdown-item" data-bs-toggle="collapse" data-bs-target="#schedule-form-{{ $post->uuid }}" aria-expanded="false" aria-controls="schedule-form-{{ $post->uuid }}">
                                        <i class="bx bx-calendar me-2 text-primary"></i> {{ __('Programmer') }}
                                    </a>
                                    <div class="collapse px-3 py-2" id="schedule-form-{{ $post->uuid }}">
                                        <form action="{{ route('backend.blog-posts.change_status', ['blogPost' => $post->uuid]) }}" method="POST" id="schedule-form-submit-{{ $post->uuid }}" class="d-flex flex-column gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="scheduled">
                                            <input type="datetime-local" name="published_at" class="form-control form-control-sm" value="{{ optional($post->published_at)->format('Y-m-d\TH:i') }}" required>
                                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                                <i class="bx bx-calendar me-1"></i> {{ __('Programmer la publication') }}
                                            </button>
                                        </form>
                                    </div>
                                </li>

                                <li>
                                    <form action="{{ route('backend.blog-posts.change_status', ['blogPost' => $post->uuid]) }}" method="POST" id="archive-form-{{ $post->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="archived">
                                        <button type="button" class="dropdown-item" onclick="confirmAction('archive-form-{{ $post->uuid }}', '{{ __('Archiver cet article ?') }}', 'Archiver', 'warning')">
                                            <i class="bx bx-archive me-2 text-secondary"></i> {{ __('Archiver') }}
                                        </button>
                                    </form>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <form action="{{ route('backend.blog-posts.destroy', ['blogPost' => $post->uuid]) }}" method="POST" id="delete-form-{{ $post->uuid }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $post->uuid }}', '{{ __('Supprimer cet article ?') }}', 'Supprimer', 'warning')">
                                            <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                        </button>
                                    </form>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <a href="{{ route('backend.blog-post-translations.trashed', ['post_uuid' => $post->uuid]) }}"
                                        class="dropdown-item text-warning">
                                        <i class="bx bx-trash me-2"></i> {{ __('Corbeille des traductions') }}
                                    </a>
                                </li>

                            </ul>
                        </div>

                    </div>

                </div>

                <div class="card-body">

                    @include('v1.backend.pages.blogs.posts.partials._show-navigation', ['post' => $post])
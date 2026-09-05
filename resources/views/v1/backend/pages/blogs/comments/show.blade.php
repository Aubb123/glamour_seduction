@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du commentaire') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail du commentaire')])

    @include('v1.backend.pages.blogs.comments.partials._show-start', ['comment' => $comment])

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> {{ __('Informations générales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_replies">
                    <i class="bx bx-corner-down-right me-1"></i> {{ __('Réponses') }}
                    @if($replies->isNotEmpty())
                        <span class="badge bg-primary ms-1">{{ $replies->count() }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_meta">
                    <i class="bx bx-time-five me-1"></i> {{ __('Métadonnées') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">

            <!-- Informations générales -->
            <div class="tab-pane fade show active" id="tab_general">
                <div class="row g-4">

                    <div class="col-md-8">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-message-square me-2 text-primary"></i>{{ __('Contenu') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="text-muted fw-semibold mb-1">{{ __('Article lié') }}</div>
                                    @if($comment->post)
                                        <a href="{{ route('backend.blog-posts.show', ['blogPost' => $comment->post->uuid, 'slug' => $comment->post?->active_translation?->slug ?? 'null']) }}"
                                            class="fw-semibold text-primary text-decoration-none">
                                            {{ $comment->post?->active_translation?->title ?? '—' }}
                                        </a>
                                    @else
                                        <code>—</code>
                                    @endif
                                </div>
                                <hr class="my-2">
                                <div class="mb-3">
                                    <div class="text-muted fw-semibold mb-1">{{ __('Auteur') }}</div>
                                    @if($comment->user)
                                        <a href="{{ route('backend.users.show', ['user' => $comment->user->uuid, 'slug' => $comment->user->slug]) }}"
                                            class="fw-semibold text-primary text-decoration-none">
                                            {{ $comment->user->first_name . ' ' . $comment->user->last_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('Anonyme') }}</span>
                                    @endif
                                </div>
                                <hr class="my-2">
                                <div>
                                    <div class="text-muted fw-semibold mb-1">{{ __('Contenu') }}</div>
                                    <div class="p-3 bg-light rounded">
                                        {!! nl2br(e($comment->content)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-pulse me-2 text-primary"></i>{{ __('Statut') }}
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column gap-3">

                                <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                        style="background: {{ $comment->is_visible ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('Modération') }}</div>
                                        <small class="text-muted">{{ __('Visibilité publique') }}</small>
                                    </div>
                                    @if ($comment->is_visible)
                                        <span class="badge bg-soft-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i>{{ __('Approuvé') }}
                                        </span>
                                    @else
                                        <span class="badge bg-soft-danger px-3 py-2">
                                            <i class="bx bx-x-circle me-1"></i>{{ __('En attente') }}
                                        </span>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Réponses -->
            <div class="tab-pane fade" id="tab_replies">
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-corner-down-right me-2 text-primary"></i>{{ __('Réponses à ce commentaire') }}
                                    <span class="badge bg-primary ms-2">{{ $replies->count() }}</span>
                                </h6>
                                <a href="{{ route('backend.blog-post-comment-replies.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-link-external me-1"></i> {{ __('Voir toutes les réponses') }}
                                </a>
                            </div>
                            <div class="card-body">
                                @forelse ($replies as $reply)
                                    <div class="border rounded p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <strong>{{ $reply->user?->name ?? __('Anonyme') }}</strong>
                                                <small class="text-muted ms-2">@formatDate($reply->created_at, 'D MMMM YYYY - HH:mm:ss')</small>
                                            </div>
                                            <div class="hstack gap-1">
                                                <a href="{{ route('backend.blog-post-comment-replies.show', ['blogPostCommentReply' => $reply->uuid]) }}" class="btn btn-sm btn-info" title="{{ __('Détails') }}">
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
                                        </div>

                                        <div class="p-3 bg-light rounded">
                                            {!! nl2br(e($reply->content)) !!}
                                        </div>

                                        <div class="mt-2">
                                            @if ($reply->is_visible)
                                                <span class="badge bg-soft-success">{{ __('Approuvé') }}</span>
                                            @else
                                                <span class="badge bg-soft-danger">{{ __('En attente') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0 text-center py-3">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucune réponse à ce commentaire.') }}
                                    </p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métadonnées -->
            <div class="tab-pane fade" id="tab_meta">
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-data me-2 text-primary"></i>{{ __('Métadonnées') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code>{{ $comment->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($comment->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                    <div class="col-8">@formatDate($comment->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                    <div class="col-8">
                                        @if ($comment->deleted_at)
                                            @formatDate($comment->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @include('v1.backend.pages.blogs.comments.partials._show-end', ['comment' => $comment])

@endsection
@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la réponse') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de la réponse')])

    @include('v1.backend.pages.blogs.replies.partials._show-start', ['reply' => $reply])

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> {{ __('Informations générales') }}
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
                                    <div class="text-muted fw-semibold mb-1">{{ __('Commentaire lié') }}</div>
                                    @if($reply->comment)
                                        <a href="{{ route('backend.blog-post-comments.show', ['blogPostComment' => $reply->comment->uuid]) }}"
                                           class="fw-semibold text-primary text-decoration-none">
                                            {{ Str::limit($reply->comment?->content ?? '—', 80) }}
                                        </a>
                                    @else
                                        <code>—</code>
                                    @endif
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Auteur') }}</div>
                                    <div class="col-8">
                                        @if($reply->user)
                                            <a href="{{ route('backend.users.show', ['user' => $reply->user->uuid, 'slug' => $reply->user->slug]) }}"
                                               class="fw-semibold text-primary text-decoration-none">
                                                {{ $reply->user->first_name . ' ' . $reply->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('Anonyme') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div>
                                    <div class="text-muted fw-semibold mb-1">{{ __('Contenu') }}</div>
                                    <div class="p-3 bg-light rounded">
                                        {!! nl2br(e($reply->content)) !!}
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
                                     style="background: {{ $reply->is_visible ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('Modération') }}</div>
                                        <small class="text-muted">{{ __('Visibilité publique') }}</small>
                                    </div>
                                    @if ($reply->is_visible)
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i>{{ __('Approuvé') }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                            <i class="bx bx-x-circle me-1"></i>{{ __('En attente') }}
                                        </span>
                                    @endif
                                </div>

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
                                    <div class="col-8"><code>{{ $reply->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($reply->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                    <div class="col-8">@formatDate($reply->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                    <div class="col-8">
                                        @if ($reply->deleted_at)
                                            @formatDate($reply->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.blogs.replies.partials._show-end', ['reply' => $reply])

@endsection
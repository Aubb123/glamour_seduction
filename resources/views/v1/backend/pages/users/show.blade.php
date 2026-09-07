@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'utilisateur') }} — {{ $user->first_name }} {{ $user->last_name }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de l\'utilisateur')])

    @include('v1.backend.pages.users.partials._show-start', ['user' => $user])

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> {{ __('Informations générales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_multimedia">
                    <i class="bx bx-image me-1"></i> {{ __('Multimédias') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">

            {{-- Informations générales --}}
            <div class="tab-pane fade show active" id="tab_general">
                <div class="row g-4">

                    <div class="col-md-8">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-info-circle me-2 text-primary"></i>{{ __('Détails') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code>{{ $user->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Email') }}</div>
                                    <div class="col-8">{{ $user->email }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Prénom') }}</div>
                                    <div class="col-8">{{ $user->first_name }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Nom') }}</div>
                                    <div class="col-8">{{ $user->last_name }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Genre') }}</div>
                                    <div class="col-8">{{ $user->gender === 'male' ? __('Masculin') : ($user->gender === 'female' ? __('Féminin') : '-') }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Pays') }}</div>
                                    <div class="col-8">
                                        <a class="text-primary" href="{{ route('backend.geocountries.show', ['geoCountry' => $user->country->uuid, 'slug' => $user->country?->active_translation?->slug ?? 'null']) }}">
                                            {{ $user->country?->active_translation?->name ?? '-' }}
                                        </a>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Slug') }}</div>
                                    <div class="col-8">{{ $user->slug }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Rôles') }}</div>
                                    <div class="col-8">
                                        @forelse ($user->roles as $role)
                                            <span class="badge bg-info me-1">{{ $role->active_translation?->label ?? $role->name }}</span>
                                        @empty
                                            <span class="text-muted">{{ __('Aucun rôle assigné.') }}</span>
                                        @endforelse
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

                                <div class="d-flex align-items-center justify-content-between p-3 rounded" >
                                    <div>
                                        <div class="fw-semibold">{{ __('Compte') }}</div>
                                        <small class="text-muted">{{ __('Statut actuel') }}</small>
                                    </div>
                                    @if ($user->status === \App\Enums\Users\UserStatus::Active)
                                        <span class="badge badge-soft-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i>{{ __('Actif') }}
                                        </span>
                                    @elseif ($user->status === \App\Enums\Users\UserStatus::Suspended)
                                        <span class="badge badge-soft-warning px-3 py-2">
                                            <i class="bx bx-pause me-1"></i>{{ __('Suspendu') }}
                                        </span>
                                    @elseif ($user->status === \App\Enums\Users\UserStatus::Banned)
                                        <span class="badge badge-soft-danger px-3 py-2">
                                            <i class="bx bx-minus me-1"></i>{{ __('Banni') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 rounded bg-light">
                                    <div>
                                        <div class="fw-semibold">{{ __('Email vérifié') }}</div>
                                        <small class="text-muted">{{ $user->email_verified_at ? formatDate($user->email_verified_at, 'D MMMM YYYY - HH:mm:ss') : __('Non vérifié') }}</small>
                                    </div>
                                    @if ($user->email_verified_at)
                                        <span class="badge badge-soft-success px-3 py-2">
                                            <i class="bx bx-check me-1"></i>{{ __('Vérifié') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-warning px-3 py-2">
                                            <i class="bx bx-x me-1"></i>{{ __('Non vérifié') }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('backend.users.activity-logs', ['user' => $user->uuid, 'slug' => $user->slug]) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('Activité') }}</div>
                                        <small class="text-muted">{{ __('Journaux d\'activité') }}</small>
                                    </div>
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="bx bx-pulse me-1"></i> {{ $user->activity_logs_count }} {{ __('entrées') }}
                                    </span>
                                </a>

                                <a href="{{ route('backend.users.subscribes', ['user' => $user->uuid, 'slug' => $user->slug]) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('Abonnements') }}</div>
                                        <small class="text-muted">{{ __('Abonnements newsletter') }}</small>
                                    </div>
                                    <span class="badge bg-info px-3 py-2">
                                        <i class="bx bx-send me-1"></i> {{ $user->subscribes_count }} {{ __('entrées') }}
                                    </span>
                                </a>

                                <a href="{{ route('backend.users.galleries', ['user' => $user->uuid, 'slug' => $user->slug]) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('Galeries') }}</div>
                                        <small class="text-muted">{{ __('Galeries de l\'utilisateur') }}</small>
                                    </div>
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="bx bx-image me-1"></i> {{ $user->galleries_count }} {{ __('entrées') }}
                                    </span>
                                </a>

                                <a href="{{ route('backend.users.audits', ['user' => $user->uuid, 'slug' => $user->slug]) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('Audits') }}</div>
                                        <small class="text-muted">{{ __('Journal des audits') }}</small>
                                    </div>
                                    <span class="badge bg-info px-3 py-2">
                                        <i class="bx bx-file me-1"></i> {{ $user->audits_count }} {{ __('entrées') }}
                                    </span>
                                </a>

                                <a href="{{ route('backend.users.blog-posts', ['user' => $user->uuid, 'slug' => $user->slug]) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('Blogs') }}</div>
                                        <small class="text-muted">{{ __('Articles de blog') }}</small>
                                    </div>
                                    <span class="badge bg-secondary px-3 py-2">
                                        <i class="bx bx-book me-1"></i> {{ $user->blog_posts_count }} {{ __('entrées') }}
                                    </span>
                                </a>

                                <a href="{{ route('backend.users.blog-post-comments', ['user' => $user->uuid, 'slug' => $user->slug]) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('Commentaires') }}</div>
                                        <small class="text-muted">{{ __('Commentaires de blog') }}</small>
                                    </div>
                                    <span class="badge bg-warning px-3 py-2">
                                        <i class="bx bx-message-square me-1"></i> {{ $user->blog_post_comments_count }} {{ __('entrées') }}
                                    </span>
                                </a>

                                <a href="{{ route('backend.users.blog-post-comment-replies', ['user' => $user->uuid, 'slug' => $user->slug]) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('Réponses') }}</div>
                                        <small class="text-muted">{{ __('Réponses aux commentaires') }}</small>
                                    </div>
                                    <span class="badge bg-danger px-3 py-2">
                                        <i class="bx bx-message-rounded me-1"></i> {{ $user->blog_post_comment_replies_count }} {{ __('entrées') }}
                                    </span>
                                </a>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Multimédias --}}
            @include('v1.backend.layouts.partials._tab-multimedia', ['entity' => $user])

        </div>

    @include('v1.backend.pages.users.partials._show-end', ['user' => $user])

@endsection
                
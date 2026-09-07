@extends('v1.backend.layouts.app')

@section('title') {{ __('Tableau de bord') }} @endsection

@section('content')

    <div class="d-lg-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1">{{ __('Bienvenue') }}, {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h2>
            <p>{{ __('Voici un aperçu de votre application.') }}</p>
        </div>
    </div>

    {{-- Cartes de statistiques --}}
    <div class="row g-3 mb-4">

        {{-- Utilisateurs --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-primary p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bx bx-user fs-4 text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold fs-4">{{ $stats['users']['total'] }}</h6>
                        <small class="text-muted">{{ __('Utilisateurs') }}</small>
                        <div class="mt-1">
                            <span class="badge badge-soft-success">{{ $stats['users']['active'] }} {{ __('actifs') }}</span>
                            <span class="badge badge-soft-warning">{{ $stats['users']['suspended'] }} {{ __('suspendus') }}</span>
                            <span class="badge badge-soft-danger">{{ $stats['users']['banned'] }} {{ __('bannis') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('backend.users.index') }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-arrow-right"></i></a>
                </div>
            </div>
        </div>

        {{-- Rôles & Permissions --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-info p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bx bx-shield-quarter fs-4 text-info"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold fs-4">{{ $stats['roles']['total'] }}</h6>
                        <small class="text-muted">{{ __('Rôles') }}</small>
                        <div class="mt-1">
                            <span class="badge badge-soft-info">{{ $stats['permissions']['total'] }} {{ __('permissions') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('backend.roles.index') }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-arrow-right"></i></a>
                </div>
            </div>
        </div>

        {{-- Abonnés --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-success p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bx bx-send fs-4 text-success"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold fs-4">{{ $stats['subscribes']['total'] }}</h6>
                        <small class="text-muted">{{ __('Abonnés') }}</small>
                        <div class="mt-1">
                            <span class="badge badge-soft-success">{{ $stats['subscribes']['active'] }} {{ __('actifs') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('backend.subscribes.index') }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-arrow-right"></i></a>
                </div>
            </div>
        </div>

        {{-- Contacts --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-warning p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bx bx-mail-send fs-4 text-warning"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold fs-4">{{ $stats['contacts']['total'] }}</h6>
                        <small class="text-muted">{{ __('Contacts') }}</small>
                        <div class="mt-1">
                            <span class="badge badge-soft-warning">{{ $stats['contacts']['unread'] }} {{ __('non lus') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('backend.contacts.index') }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-arrow-right"></i></a>
                </div>
            </div>
        </div>

        {{-- Articles de blog --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-secondary p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bx bx-book fs-4 text-secondary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold fs-4">{{ $stats['blogPosts']['total'] }}</h6>
                        <small class="text-muted">{{ __('Articles') }}</small>
                        <div class="mt-1">
                            <span class="badge badge-soft-success">{{ $stats['blogPosts']['published'] }} {{ __('publiés') }}</span>
                            <span class="badge badge-soft-warning">{{ $stats['blogPosts']['drafts'] }} {{ __('brouillons') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('backend.blog-posts.index') }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-arrow-right"></i></a>
                </div>
            </div>
        </div>

        {{-- Commentaires --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-danger p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bx bx-message-square fs-4 text-danger"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold fs-4">{{ $stats['blogComments']['total'] }}</h6>
                        <small class="text-muted">{{ __('Commentaires') }}</small>
                        <div class="mt-1">
                            <span class="badge badge-soft-success">{{ $stats['blogComments']['approved'] }} {{ __('approuvés') }}</span>
                            <span class="badge badge-soft-warning">{{ $stats['blogComments']['pending'] }} {{ __('en attente') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('backend.blog-post-comments.index') }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-arrow-right"></i></a>
                </div>
            </div>
        </div>

        {{-- Galeries --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-success p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bx bx-image fs-4 text-success"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold fs-4">{{ $stats['galleries']['total'] }}</h6>
                        <small class="text-muted">{{ __('Galeries') }}</small>
                        <div class="mt-1">
                            <span class="badge badge-soft-success">{{ $stats['galleries']['active'] }} {{ __('actives') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('backend.galleries.index') }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-arrow-right"></i></a>
                </div>
            </div>
        </div>

        {{-- FAQ --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-info p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bx bx-help-circle fs-4 text-info"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold fs-4">{{ $stats['faqs']['total'] }}</h6>
                        <small class="text-muted">{{ __('FAQs') }}</small>
                        <div class="mt-1">
                            <span class="badge badge-soft-success">{{ $stats['faqs']['active'] }} {{ __('actives') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('backend.faqs.index') }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-arrow-right"></i></a>
                </div>
            </div>
        </div>

        {{-- Cloud --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-secondary p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bx bx-cloud fs-4 text-secondary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold fs-4">{{ $stats['cloudPlatforms']['total'] }}</h6>
                        <small class="text-muted">{{ __('Plateformes Cloud') }}</small>
                        <div class="mt-1">
                            <span class="badge badge-soft-info">{{ $stats['cloudFolders'] }} {{ __('dossiers') }}</span>
                            <span class="badge badge-soft-warning">{{ $stats['cloudFiles'] }} {{ __('fichiers') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('backend.cloud-platforms.index') }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-arrow-right"></i></a>
                </div>
            </div>
        </div>

        {{-- Tags & Catégories --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-warning p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bx bx-tag fs-4 text-warning"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold fs-4">{{ $stats['blogTags'] }}</h6>
                        <small class="text-muted">{{ __('Tags') }}</small>
                        <div class="mt-1">
                            <span class="badge badge-soft-info">{{ $stats['blogCategories'] }} {{ __('catégories') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('backend.blog-tags.index') }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-arrow-right"></i></a>
                </div>
            </div>
        </div>

        {{-- Audits --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-danger p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bx bx-file fs-4 text-danger"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold fs-4">{{ $stats['audits'] }}</h6>
                        <small class="text-muted">{{ __('Audits') }}</small>
                    </div>
                    <a href="{{ route('backend.audits.index') }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-arrow-right"></i></a>
                </div>
            </div>
        </div>

        {{-- Logs d'activité --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-soft-primary p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bx bx-pulse fs-4 text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold fs-4">{{ $stats['activityLogs'] }}</h6>
                        <small class="text-muted">{{ __('Logs d\'activité') }}</small>
                    </div>
                    <a href="{{ route('backend.activity-logs.index') }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}"><i class="bx bx-arrow-right"></i></a>
                </div>
            </div>
        </div>

    </div>

    {{-- Listes récentes --}}
    <div class="row">

        {{-- Derniers utilisateurs --}}
        <div class="col-xxl-4 col-xl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                    <h5 class="mb-2">{{ __('Derniers utilisateurs') }}</h5>
                    <a href="{{ route('backend.users.index') }}" class="btn btn-light btn-sm mb-2">{{ __('Voir tout') }}</a>
                </div>
                <div class="card-body pb-2">
                    @forelse ($latestUsers as $user)
                        <div class="d-sm-flex justify-content-between flex-wrap mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <a href="{{ route('backend.users.show', ['user' => $user->uuid, 'slug' => $user->slug]) }}" class="avatar bg-gray-100 rounded-circle flex-shrink-0">
                                    <img src="{{ $user->getFirstImage()['url_img'] }}" class="img-fluid" alt="{{ $user->first_name }}" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
                                </a>
                                <div class="ms-2 flex-fill">
                                    <h6 class="fs-medium text-truncate mb-1"><a href="{{ route('backend.users.show', ['user' => $user->uuid, 'slug' => $user->slug]) }}">{{ $user->first_name }} {{ $user->last_name }}</a></h6>
                                    <p class="fs-13 d-inline-flex align-items-center">
                                        <span class="text-info">{{ $user->email }}</span>
                                        <i class="bx bxs-circle fs-6 text-primary mx-1"></i>@formatDate($user->created_at, 'D MMM YYYY')
                                    </p>
                                </div>
                            </div>
                            <div class="text-sm-end mb-2">
                                @if ($user->status === \App\Enums\Users\UserStatus::Active)
                                    <span class="badge badge-soft-success">{{ __('Actif') }}</span>
                                @elseif ($user->status === \App\Enums\Users\UserStatus::Suspended)
                                    <span class="badge badge-soft-warning">{{ __('Suspendu') }}</span>
                                @elseif ($user->status === \App\Enums\Users\UserStatus::Banned)
                                    <span class="badge badge-soft-danger">{{ __('Banni') }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">{{ __('Aucun utilisateur.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Derniers contacts --}}
        <div class="col-xxl-4 col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                    <h5 class="mb-2">{{ __('Derniers messages') }}</h5>
                    <a href="{{ route('backend.contacts.index') }}" class="btn btn-light btn-sm mb-2">{{ __('Voir tout') }}</a>
                </div>
                <div class="card-body pb-2">
                    @forelse ($latestContacts as $contact)
                        <div class="d-sm-flex justify-content-between flex-wrap mb-3">
                            <div class="d-flex align-items-center mb-2">
								@if($contact->user && $contact->user->getFirstImage())
									<a href="{{ route('backend.contacts.show', ['contact' => $contact->uuid]) }}" class="avatar bg-gray-100 rounded-circle flex-shrink-0">
										<img src="{{ $contact->user->getFirstImage()['url_img'] }}" class="img-fluid" alt="{{ $contact->fullname }}" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
									</a>
								@else
									<span class="avatar bg-gray-100 rounded-circle flex-shrink-0">
										<i class="bx bx-user fs-5"></i>
									</span>
								@endif
                                <div class="ms-2 flex-fill">
                                    <h6 class="fs-medium text-truncate mb-1"><a href="{{ route('backend.contacts.show', ['contact' => $contact->uuid]) }}">{{ $contact->fullname }}</a></h6>
                                    <p class="fs-13 d-inline-flex align-items-center">
                                        <span class="text-info">{{ $contact->email }}</span>
                                        <i class="bx bxs-circle fs-6 text-primary mx-1"></i>@formatDate($contact->created_at, 'D MMM YYYY')
                                    </p>
                                </div>
                            </div>
                            <div class="text-sm-end mb-2">
                                @if ($contact->is_read)
                                    <span class="badge badge-soft-success">{{ __('Lu') }}</span>
                                @else
                                    <span class="badge badge-soft-warning">{{ __('Non lu') }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">{{ __('Aucun message.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Derniers articles --}}
        <div class="col-xxl-4 col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                    <h5 class="mb-2">{{ __('Derniers articles') }}</h5>
                    <a href="{{ route('backend.blog-posts.index') }}" class="btn btn-light btn-sm mb-2">{{ __('Voir tout') }}</a>
                </div>
                <div class="card-body pb-2">
                    @forelse ($latestPosts as $post)
                        <div class="d-sm-flex justify-content-between flex-wrap mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <a href="{{ route('backend.blog-posts.show', ['blogPost' => $post->uuid, 'slug' => $post->active_translation?->slug ?? 'null']) }}" class="avatar bg-gray-100 rounded-circle flex-shrink-0">
                                    <img src="{{ $post->getFirstImage()['url_img'] }}" class="img-fluid" alt="{{ $post->active_translation?->title ?? 'Article' }}" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
                                </a>
                                <div class="ms-2 flex-fill">
                                    <h6 class="fs-medium text-truncate mb-1"><a href="{{ route('backend.blog-posts.show', ['blogPost' => $post->uuid, 'slug' => $post->active_translation?->slug ?? 'null']) }}">{{ Str::limit($post->active_translation?->title ?? __('Sans titre'), 30) }}</a></h6>
                                    <p class="fs-13 d-inline-flex align-items-center">
                                        <span class="text-info">{{ $post->user?->first_name }} {{ $post->user?->last_name }}</span>
                                        <i class="bx bxs-circle fs-6 text-primary mx-1"></i>@formatDate($post->created_at, 'D MMM YYYY')
                                    </p>
                                </div>
                            </div>
                            <div class="text-sm-end mb-2">
                                @if ($post->status === \App\Enums\Blogs\Posts\BlogPostStatus::Published)
                                    <span class="badge badge-soft-success">{{ __('Publié') }}</span>
                                @elseif ($post->status === \App\Enums\Blogs\Posts\BlogPostStatus::Scheduled)
                                    <span class="badge badge-soft-warning">{{ __('Programmé') }}</span>
                                @elseif ($post->status === \App\Enums\Blogs\Posts\BlogPostStatus::Archived)
                                    <span class="badge badge-soft-secondary">{{ __('Archivé') }}</span>
                                @else
                                    <span class="badge badge-soft-danger">{{ __('Brouillon') }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">{{ __('Aucun article.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Derniers abonnés --}}
        <div class="col-xxl-4 col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                    <h5 class="mb-2">{{ __('Derniers abonnés') }}</h5>
                    <a href="{{ route('backend.subscribes.index') }}" class="btn btn-light btn-sm mb-2">{{ __('Voir tout') }}</a>
                </div>
                <div class="card-body pb-2">
                    @forelse ($latestSubscribes as $subscribe)
                        <div class="d-sm-flex justify-content-between flex-wrap mb-3">
                            <div class="d-flex align-items-center mb-2">
								@if($subscribe->user && $subscribe->user->getFirstImage())
									<a href="{{ route('backend.subscribes.show', ['subscribe' => $subscribe->uuid]) }}" class="avatar bg-gray-100 rounded-circle flex-shrink-0">
										<img src="{{ $subscribe->user->getFirstImage()['url_img'] }}" class="img-fluid" alt="{{ $subscribe->email }}" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
									</a>
								@else
									<span class="avatar bg-gray-100 rounded-circle flex-shrink-0">
										<i class="bx bx-send fs-5"></i>
									</span>
								@endif
                                <div class="ms-2 flex-fill">
                                    <h6 class="fs-medium text-truncate mb-1"><a href="{{ route('backend.subscribes.show', ['subscribe' => $subscribe->uuid]) }}">{{ $subscribe->email }}</a></h6>
                                    <p class="fs-13 d-inline-flex align-items-center">
                                        <span class="text-info">{{ $subscribe->user?->first_name }} {{ $subscribe->user?->last_name }}</span>
                                        <i class="bx bxs-circle fs-6 text-primary mx-1"></i>@formatDate($subscribe->created_at, 'D MMM YYYY')
                                    </p>
                                </div>
                            </div>
                            <div class="text-sm-end mb-2">
                                <span class="{{ $subscribe->status?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                    {{ $subscribe->status?->label() ?? __("Inconnu") }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">{{ __('Aucun abonné.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

@endsection
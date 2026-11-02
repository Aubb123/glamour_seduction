@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'article') }} — <code>{{ $post->active_translation?->title ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de l\'article')])

        @include('v1.backend.pages.blogs.posts.partials._show-start', ['post' => $post])

            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                        <i class="bx bx-info-circle me-1"></i> {{ __('Informations générales') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_translations">
                        <i class="bx bx-globe me-1"></i> {{ __('Traductions') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_comments">
                        <i class="bx bx-message-square me-1"></i> {{ __('Commentaires') }}
                        @if($comments->isNotEmpty())
                            <span class="badge bg-primary ms-1">{{ $comments->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_multimedia">
                        <i class="bx bx-image me-1"></i> {{ __('Multimédias') }}
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
                                        <i class="bx bx-file me-2 text-primary"></i>{{ __('Détails') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                        <div class="col-8"><code>{{ $post->uuid }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Catégorie') }}</div>
                                        <div class="col-8 fw-semibold">
                                            @if($post->category)
                                                <a href="{{ route('backend.blog-categories.show', ['blogCategory' => $post->category->uuid, 'slug' => $post->category?->active_translation?->slug ?? 'null']) }}"
                                                    class="badge badge-soft-primary text-decoration-none">
                                                    {{ $post->category?->active_translation?->name ?? '—' }}
                                                </a>
                                            @else
                                                <span class="badge badge-soft-primary">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Auteur') }}</div>
                                        <div class="col-8">
                                            @if($post->user)
                                                <a href="{{ route('backend.users.show', ['user' => $post->user->uuid, 'slug' => $post->user->slug]) }}"
                                                    class="fw-semibold text-primary text-decoration-none">
                                                    {{ $post->user->first_name . ' ' . $post->user->last_name }}
                                                </a>
                                            @else
                                                <span class="text-muted">{{ __('Anonyme') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Vues') }}</div>
                                        <div class="col-8">{{ number_format($post->views) }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Temps de lecture') }}</div>
                                        <div class="col-8">{{ $post->time_read ? $post->time_read . ' min' : '—' }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Ordre') }}</div>
                                        <div class="col-8">{{ $post->sort_order }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row align-items-start">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Tags') }}</div>
                                        <div class="col-8">
                                            @forelse ($post->tags as $tag)
                                                <a href="{{ route('backend.blog-tags.show', ['blogTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}"
                                                    class="badge badge-soft-info me-1 text-decoration-none">
                                                    {{ $tag->active_translation?->name ?? '#' . $tag->id }}
                                                </a>
                                            @empty
                                                <span class="text-muted">—</span>
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

                                    <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                            style="background: {{ $post->status === \App\Enums\Blogs\Posts\BlogPostStatus::Published ? '#e8f9f0' : '#fef0f0' }}">
                                        <div>
                                            <div class="fw-semibold">{{ __('Statut') }}</div>
                                            <small class="text-muted">
                                                @if ($post->status === \App\Enums\Blogs\Posts\BlogPostStatus::Published)
                                                    {{ __('Publication en ligne') }}
                                                @elseif ($post->status === \App\Enums\Blogs\Posts\BlogPostStatus::Scheduled)
                                                    {{ __('Programmé') }} @if($post->published_at) · @formatDate($post->published_at, 'D MMMM YYYY - HH:mm') @endif
                                                @elseif ($post->status === \App\Enums\Blogs\Posts\BlogPostStatus::Draft)
                                                    {{ __('Brouillon') }}
                                                @elseif ($post->status === \App\Enums\Blogs\Posts\BlogPostStatus::Archived)
                                                    {{ __('Archivé') }}
                                                @else
                                                    {{ __('Statut inconnu') }}
                                                @endif
                                            </small>
                                        </div>
                                        <span class="{{ $post->status?->badgeClass() ?? 'badge badge-soft-secondary' }} px-3 py-2">
                                            {{ $post->status?->label() ?? '—' }}
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Traductions -->
                <div class="tab-pane fade" id="tab_translations">

                    <ul class="nav nav-pills mb-3">
                        @foreach ($langs as $index => $lang)
                            <li class="nav-item">
                                <a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#trans_{{ $lang->code }}">
                                    {{ $lang->name }}
                                </a>
                            </li>
                        @endforeach
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#trans_trash">
                                <i class="bx bx-trash me-1 text-warning"></i> {{ __('Corbeille') }}
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        @foreach ($langs as $index => $lang)
                            @php($translation = getTranslation($post, $lang->id))
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="trans_{{ $lang->code }}">
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0 fw-bold">
                                                    <i class="bx bx-font me-2 text-primary"></i>{{ $lang->name }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-3 align-items-center">
                                                    <div class="col-2 text-muted fw-semibold">{{ __('Titre') }}</div>
                                                    <div class="col-9 fw-bold">{{ $translation?->title ?? '—' }}</div>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row mb-3 align-items-center">
                                                    <div class="col-2 text-muted fw-semibold">{{ __('Slug') }}</div>
                                                    <div class="col-9"><code>{{ $translation?->slug ?? '—' }}</code></div>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row mb-3 align-items-start">
                                                    <div class="col-2 text-muted fw-semibold">{{ __('Contenu') }}</div>
                                                    <div class="col-9" style="max-height: 200px; overflow-y: auto;">
                                                        {!! $translation?->content ?? '—' !!}
                                                    </div>
                                                </div>
                                                <hr class="my-2">

                                                <div class="row align-items-start">
                                                    <div class="col-2 text-muted fw-semibold">{{ __('Actions') }}</div>
                                                    <div class="col-9">
                                                        @if($translation && !$translation->trashed())
                                                            <form action="{{ route('backend.blog-post-translations.destroy', ['uuid' => $translation->uuid]) }}" method="POST" id="delete-form-{{ $translation->uuid }}" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $translation->uuid }}', '{{ __('Supprimer cette traduction ?') }}', 'Supprimer', 'warning')">
                                                                    <i class="bx bx-trash me-1"></i> {{ __('Supprimer') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="tab-pane fade" id="trans_trash">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="card border">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bx bx-trash me-2 text-warning"></i>{{ __('Corbeille des traductions') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if ($blogPostTranslations->isEmpty())
                                                <p class="text-muted mb-0">{{ __('Aucune traduction supprimée.') }}</p>
                                            @else
                                                @include('v1.backend.pages.blogs.posts.partials._trashed-table-translations', [
                                                    'blogPostTranslations' => $blogPostTranslations,
                                                    'post' => $post
                                                ])
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Commentaires -->
                @include('v1.backend.pages.blogs.posts.partials._section_comments_and_replies', [
                    'post' => $post,
                    'comments' => $comments,
                ])

                <!-- Multimédias -->
                @include('v1.backend.layouts.partials._tab-multimedia', ['entity' => $post])

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
                                        <div class="col-8"><code>{{ $post->uuid }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Publié le') }}</div>
                                        <div class="col-8">
                                            @if ($post->published_at)
                                                @formatDate($post->published_at, 'D MMMM YYYY - HH:mm:ss')
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                        <div class="col-8">@formatDate($post->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                        <div class="col-8">@formatDate($post->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                        <div class="col-8">
                                            @if ($post->deleted_at)
                                                @formatDate($post->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

        @include('v1.backend.pages.blogs.posts.partials._show-end', ['post' => $post])
                    
@endsection

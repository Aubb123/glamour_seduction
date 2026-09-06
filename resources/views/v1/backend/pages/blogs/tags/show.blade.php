@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du tag') }} — <code>{{ $tag->active_translation?->name ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail du tag')])

    @include('v1.backend.pages.blogs.tags.partials._show-start', ['tag' => $tag])

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
                <a class="nav-link" data-bs-toggle="tab" href="#tab_meta">
                    <i class="bx bx-time-five me-1"></i> {{ __('Métadonnées') }}
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
                                    <div class="col-8"><code>{{ $tag->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Ordre de tri') }}</div>
                                    <div class="col-8 fw-semibold">{{ $tag->sort_order }}</div>
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
                                     style="background: {{ $tag->status === \App\Enums\Blogs\Tags\BlogTagStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('Activation') }}</div>
                                        <small class="text-muted">{{ __('Visibilité interne') }}</small>
                                    </div>
                                    @if ($tag->status === \App\Enums\Blogs\Tags\BlogTagStatus::Active)
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i>{{ __('Actif') }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                            <i class="bx bx-x-circle me-1"></i>{{ __('Inactif') }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('backend.blog-tags.posts', ['blogTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('Articles') }}</div>
                                        <small class="text-muted">{{ __('Articles de blog') }}</small>
                                    </div>
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="bx bx-file me-1"></i> {{ $tag->posts_count ?? $posts->count() }} {{ __('entrées') }}
                                    </span>
                                </a>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Traductions --}}
            <div class="tab-pane fade" id="tab_translations">
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-globe me-2 text-primary"></i>{{ __('Traductions') }}
                                </h6>
                            </div>
                            <div class="card-body">

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
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="trans_{{ $lang->code }}">
                                            <div class="row g-4">
                                                <div class="col-md-8">
                                                    <div class="card border">
                                                        <div class="card-header bg-light py-2">
                                                            <h6 class="mb-0 fw-bold">
                                                                <i class="bx bx-font me-2 text-primary"></i>{{ $lang->name }}
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row mb-3 align-items-center">
                                                                <div class="col-3 text-muted fw-semibold">{{ __('Nom') }}</div>
                                                                <div class="col-9 fw-bold">{{ getTranslation($tag, $lang->id)?->name ?? '—' }}</div>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="row mb-3 align-items-center">
                                                                <div class="col-3 text-muted fw-semibold">{{ __('Slug') }}</div>
                                                                <div class="col-9"><code>{{ getTranslation($tag, $lang->id)?->slug ?? '—' }}</code></div>
                                                            </div>

                                                            @if(getTranslation($tag, $lang->id) && !getTranslation($tag, $lang->id)->trashed())
                                                                <hr class="my-2">
                                                                <div class="row align-items-start">
                                                                    <div class="col-3 text-muted fw-semibold">{{ __('Actions') }}</div>
                                                                    <div class="col-9">
                                                                        <form action="{{ route('backend.blog-tag-translations.destroy', ['uuid' => getTranslation($tag, $lang->id)->uuid]) }}" method="POST" id="delete-translation-form-{{ getTranslation($tag, $lang->id)->uuid }}" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmAction('delete-translation-form-{{ getTranslation($tag, $lang->id)->uuid }}', '{{ __('Supprimer cette traduction ?') }}', 'Supprimer', 'warning')">
                                                                                <i class="bx bx-trash me-1"></i> {{ __('Supprimer') }}
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            @endif

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
                                                        @include('v1.backend.pages.blogs.tags.partials._trashed-table-translations', [
                                                            'blogTagTranslations' => $blogTagTranslations,
                                                            'tag' => $tag
                                                        ])
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Métadonnées --}}
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
                                    <div class="col-8"><code>{{ $tag->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($tag->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                    <div class="col-8">@formatDate($tag->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                    <div class="col-8">
                                        @if ($tag->deleted_at)
                                            @formatDate($tag->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.blogs.tags.partials._show-end', ['tag' => $tag])

@endsection
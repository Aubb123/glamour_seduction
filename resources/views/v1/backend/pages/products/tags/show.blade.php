@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du tag') }} — <code>{{ $tag->active_translation?->name ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de la tag')])

    @include('v1.backend.pages.products.tags.partials._show-start', ['tag' => $tag])

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
                                    <i class="bx bx-file me-2 text-primary"></i>{{ __('Détails') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Nom') }}</div>
                                    <div class="col-8 fw-bold">{{ $tag->active_translation?->name ?? '—' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code>{{ $tag->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
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
                                        style="background: {{ $tag->status === \App\Enums\Products\Tags\ProductTagStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('Statut') }}</div>
                                        <small class="text-muted">
                                            @if ($tag->status === \App\Enums\Products\Tags\ProductTagStatus::Active)
                                                {{ __('Tag visible publiquement') }}
                                            @elseif ($tag->status === \App\Enums\Products\Tags\ProductTagStatus::Inactive)
                                                {{ __('Tag masqué temporairement') }}
                                            @else
                                                {{ __('Statut inconnu') }}
                                            @endif
                                        </small>
                                    </div>
                                    <span class="{{ $tag->status?->badgeClass() ?? 'badge badge-soft-secondary' }} px-3 py-2">
                                        {{ $tag->status?->label() ?? '—' }}
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Traductions --}}
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
                        @php($translation = getTranslation($tag, $lang->id))
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
                                                <div class="col-2 text-muted fw-semibold">{{ __('Nom') }}</div>
                                                <div class="col-9 fw-bold">{{ $translation?->name ?? '—' }}</div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-2 text-muted fw-semibold">{{ __('Slug') }}</div>
                                                <div class="col-9"><code>{{ $translation?->slug ?? '—' }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-start">
                                                <div class="col-2 text-muted fw-semibold">{{ __('Description') }}</div>
                                                <div class="col-9">
                                                    @if ($translation?->description)
                                                        {{ $translation->description }}
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <hr class="my-2">

                                            <div class="row align-items-start">
                                                <div class="col-2 text-muted fw-semibold">{{ __('Actions') }}</div>
                                                <div class="col-9">
                                                    @if($translation && !$translation->trashed())
                                                        <form action="{{ route('backend.product-tag-translations.destroy', ['uuid' => $translation->uuid]) }}" method="POST" id="delete-form-{{ $translation->uuid }}" class="d-inline">
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
                                        @if ($productTagTranslations->isEmpty())
                                            <p class="text-muted mb-0">{{ __('Aucune traduction supprimée.') }}</p>
                                        @else
                                            @include('v1.backend.pages.products.tags.partials._trashed-table-translations', [
                                                'productTagTranslations' => $productTagTranslations,
                                                'tag' => $tag
                                            ])
                                        @endif
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

    @include('v1.backend.pages.products.tags.partials._show-end', ['tag' => $tag])

@endsection
@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la catégorie') }} — <code>{{ $category->active_translation?->name ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de la catégorie')])

    @include('v1.backend.pages.products.categories.partials._show-start', ['category' => $category])

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
                                    <div class="col-8 fw-bold">{{ $category->active_translation?->name ?? '—' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Catégorie parente') }}</div>
                                    <div class="col-8 fw-semibold">
                                        @if ($category->parent)
                                            <a href="{{ route('backend.product-categories.show', ['productCategory' => $category->parent->uuid, 'slug' => $category->parent->active_translation?->slug ?? 'null']) }}"
                                                class="badge badge-soft-primary text-decoration-none">
                                                {{ $category->parent->active_translation?->name ?? $category->parent->uuid }}
                                            </a>
                                        @else
                                            <span class="badge badge-soft-secondary">{{ __('Racine') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code>{{ $category->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Ordre de tri') }}</div>
                                    <div class="col-8 fw-semibold">{{ $category->sort_order }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Sous-catégories') }}</div>
                                    <div class="col-8 fw-semibold">{{ number_format($category->children_count ?? 0) }}</div>
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
                                        style="background: {{ $category->status === \App\Enums\Products\Categories\ProductCategoryStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('Statut') }}</div>
                                        <small class="text-muted">
                                            @if ($category->status === \App\Enums\Products\Categories\ProductCategoryStatus::Active)
                                                {{ __('Catégorie visible publiquement') }}
                                            @elseif ($category->status === \App\Enums\Products\Categories\ProductCategoryStatus::Inactive)
                                                {{ __('Catégorie masquée temporairement') }}
                                            @else
                                                {{ __('Statut inconnu') }}
                                            @endif
                                        </small>
                                    </div>
                                    <span class="{{ $category->status?->badgeClass() ?? 'badge badge-soft-secondary' }} px-3 py-2">
                                        {{ $category->status?->label() ?? '—' }}
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
                        @php($translation = getTranslation($category, $lang->id))
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
                                                        <form action="{{ route('backend.product-category-translations.destroy', ['uuid' => $translation->uuid]) }}" method="POST" id="delete-form-{{ $translation->uuid }}" class="d-inline">
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
                                        @if ($productCategoryTranslations->isEmpty())
                                            <p class="text-muted mb-0">{{ __('Aucune traduction supprimée.') }}</p>
                                        @else
                                            @include('v1.backend.pages.products.categories.partials._trashed-table-translations', [
                                                'productCategoryTranslations' => $productCategoryTranslations,
                                                'category' => $category
                                            ])
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Multimédias --}}
            @include('v1.backend.layouts.partials._tab-multimedia', ['entity' => $category])

            {{-- Métadonnées --}}
            <div class="tab-pane fade" id="tab_meta">
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-data me-2 text-primary"></i>Métadonnées
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code>{{ $category->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($category->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                    <div class="col-8">@formatDate($category->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                    <div class="col-8">
                                        @if ($category->deleted_at)
                                            @formatDate($category->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.products.categories.partials._show-end', ['category' => $category])

@endsection

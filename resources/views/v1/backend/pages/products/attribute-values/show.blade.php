@extends('v1.backend.layouts.app')

@section('title')
    {{ __("Détail de la valeur d'attribut") }} — <code>{{ $value->active_translation?->label ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __("Détail de la valeur d'attribut")])

    @include('v1.backend.pages.products.attribute-values.partials._show-start', ['value' => $value])

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
                                        <div class="col-8"><code>{{ $value->uuid }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Attribut') }}</div>
                                        <div class="col-8 fw-semibold">
                                            @if($value->attribute)
                                                <a href="{{ route('backend.product-attributes.show', ['productAttribute' => $value->attribute->uuid, 'slug' => $value->attribute->active_translation?->slug ?? 'null']) }}"
                                                    class="badge badge-soft-primary text-decoration-none">
                                                    {{ $value->attribute?->active_translation?->name ?? '—' }}
                                                </a>
                                            @else
                                                <span class="badge badge-soft-primary">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __("Clé machine") }}</div>
                                        <div class="col-8"><code>{{ $value->value }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __("Ordre de tri") }}</div>
                                        <div class="col-8">{{ $value->sort_order }}</div>
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
                                            style="background: {{ $value->status === \App\Enums\Products\Attributes\ProductAttributeValueStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                        <div>
                                            <div class="fw-semibold">{{ __('Statut') }}</div>
                                            <small class="text-muted">
                                                @if ($value->status === \App\Enums\Products\Attributes\ProductAttributeValueStatus::Active)
                                                    {{ __("Valeur utilisable dans les sélecteurs de produits") }}
                                                @else
                                                    {{ __("Valeur non utilisable actuellement") }}
                                                @endif
                                            </small>
                                        </div>
                                        <span class="{{ $value->status?->badgeClass() ?? 'badge badge-soft-secondary' }} px-3 py-2">
                                            {{ $value->status?->label() ?? '—' }}
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
                            @php($translation = getTranslation($value, $lang->id))
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
                                                    <div class="col-2 text-muted fw-semibold">{{ __('Libellé') }}</div>
                                                    <div class="col-9 fw-bold">{{ $translation?->label ?? '—' }}</div>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row mb-3 align-items-center">
                                                    <div class="col-2 text-muted fw-semibold">{{ __('Slug') }}</div>
                                                    <div class="col-9"><code>{{ $translation?->slug ?? '—' }}</code></div>
                                                </div>
                                                <hr class="my-2">

                                                <div class="row align-items-start">
                                                    <div class="col-2 text-muted fw-semibold">{{ __('Actions') }}</div>
                                                    <div class="col-9">
                                                        @if($translation && !$translation->trashed())
                                                            <form action="{{ route('backend.product-attribute-value-translations.destroy', ['uuid' => $translation->uuid]) }}" method="POST" id="delete-form-{{ $translation->uuid }}" class="d-inline">
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
                                            @if ($productAttributeValueTranslations->isEmpty())
                                                <p class="text-muted mb-0">{{ __('Aucune traduction supprimée.') }}</p>
                                            @else
                                                @include('v1.backend.pages.products.attribute-values.partials._trashed-table-translations', [
                                                    'productAttributeValueTranslations' => $productAttributeValueTranslations,
                                                    'value' => $value
                                                ])
                                            @endif
                                        </div>
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
                                        <div class="col-8"><code>{{ $value->uuid }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                        <div class="col-8">@formatDate($value->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                        <div class="col-8">@formatDate($value->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                        <div class="col-8">
                                            @if ($value->deleted_at)
                                                @formatDate($value->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.products.attribute-values.partials._show-end', ['value' => $value])

@endsection
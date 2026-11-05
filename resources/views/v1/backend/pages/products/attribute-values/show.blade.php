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
                        @if($value->translations->isNotEmpty())
                            <span class="badge bg-primary ms-1">{{ $value->translations->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_trashed_translations">
                        <i class="bx bx-trash me-1"></i> {{ __('Traductions supprimées') }}
                        @if($productAttributeValueTranslations->isNotEmpty())
                            <span class="badge bg-primary ms-1">{{ $productAttributeValueTranslations->count() }}</span>
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
                                        <i class="bx bx-info-circle me-2 text-primary"></i>{{ __('Détails') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Attribut') }}</div>
                                        <div class="col-8">{{ $value->attribute?->active_translation?->name ?? '—' }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __("Clé machine") }}</div>
                                        <div class="col-8"><code>{{ $value->value }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __("Ordre de tri") }}</div>
                                        <div class="col-8 fw-semibold">{{ $value->sort_order }}</div>
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
                                <div class="card-body text-center">
                                    <span class="badge {{ $value->status->badgeClass() }} fs-14 px-3 py-2">
                                        {{ $value->status->label() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Traductions -->
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
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Langue') }}</th>
                                                    <th>{{ __('Libellé') }}</th>
                                                    <th>Slug</th>
                                                    <th>{{ __('Créé le') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($value->translations as $translation)
                                                    <tr>
                                                        <td>{{ strtoupper($translation->lang->code ?? '—') }}</td>
                                                        <td>{{ $translation->label }}</td>
                                                        <td><code>{{ $translation->slug }}</code></td>
                                                        <td>@formatDate($translation->created_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted py-4">{{ __('Aucune traduction trouvée.') }}</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Traductions supprimées -->
                <div class="tab-pane fade" id="tab_trashed_translations">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="card border">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bx bx-trash me-2 text-danger"></i>{{ __('Traductions supprimées') }}
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

                <!-- Métadonnées -->
                <div class="tab-pane fade" id="tab_meta">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="card border">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bx bx-time-five me-2 text-primary"></i>{{ __('Métadonnées') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">UUID</div>
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
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                        <div class="col-8">
                                            @if ($value->deleted_at)
                                                @formatDate($value->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                            @else
                                                —
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
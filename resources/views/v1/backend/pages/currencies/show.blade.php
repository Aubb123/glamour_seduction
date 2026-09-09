@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/currencies/show.pageTitle') }} — {{ $currency->code }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/currencies/show.pageTitle')])

    @include('v1.backend.pages.currencies.partials._show-start', ['currency' => $currency])

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> {{ __('v1/backend/views/pages/currencies/show.informationsGenerales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_translations">
                    <i class="bx bx-globe me-1"></i> {{ __('v1/backend/views/pages/currencies/show.traductions') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_meta">
                    <i class="bx bx-time-five me-1"></i> {{ __('v1/backend/views/pages/currencies/show.metadonnees') }}
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
                                    <i class="bx bx-coin me-2 text-primary"></i>{{ __('v1/backend/views/pages/currencies/show.details') }}
                                </h6>
                            </div>
                            <div class="card-body">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.symbole') }}</div>
                                    <div class="col-8">
                                        <span class="badge badge-soft-success fs-5 fw-bold">{{ $currency->symbol }}</span>
                                    </div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.code') }}</div>
                                    <div class="col-8">
                                        <span class="badge badge-soft-primary fs-13">{{ $currency->code }}</span>
                                    </div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.codeNumerique') }}</div>
                                    <div class="col-8">
                                        <span class="badge badge-soft-secondary fs-13">{{ $currency->numeric_code ?? '—' }}</span>
                                    </div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.decimales') }}</div>
                                    <div class="col-8 fw-semibold">{{ $currency->decimal_places }}</div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.deviseParDefaut') }}</div>
                                    <div class="col-8">
                                        @if ($currency->is_default)
                                            <span class="badge bg-soft-success"><i class="bx bx-star me-1"></i>{{ __('v1/backend/views/pages/currencies/show.oui') }}</span>
                                        @else
                                            <span class="badge badge-soft-secondary">{{ __('v1/backend/views/pages/currencies/show.non') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.ordre') }}</div>
                                    <div class="col-8 fw-semibold">{{ $currency->sort_order }}</div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.statut') }}</div>
                                    <div class="col-8">
                                        <span class="badge {{ $currency->status?->badgeClass() }}">{{ $currency->status?->label() }}</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-pulse me-2 text-primary"></i>{{ __('v1/backend/views/pages/currencies/show.activation') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted">{{ __('v1/backend/views/pages/currencies/show.visibiliteDansApp') }}</span>
                                        <span class="badge {{ $currency->status?->badgeClass() }}">{{ $currency->status?->label() }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted">{{ __('v1/backend/views/pages/currencies/show.pays') }}</span>
                                        <a href="{{ route('backend.currencies.countries', ['currency' => $currency->uuid, 'slug' => $currency->active_translation?->slug ?? $currency->code]) }}" class="text-decoration-none">
                                            <span class="badge badge-soft-primary">{{ $currency->countries_count ?? 0 }}</span>
                                        </a>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted">{{ __('v1/backend/views/pages/currencies/show.corbeille') }}</span>
                                        <a href="{{ route('backend.currencies.translation.trashed', ['currency' => $currency->uuid]) }}" class="text-decoration-none">
                                            <span class="badge badge-soft-warning">{{ $currencyTranslations->count() }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Traductions -->
            <div class="tab-pane fade" id="tab_translations">
                <div class="row g-4">

                    <div class="col-md-8">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-globe me-2 text-primary"></i>{{ __('v1/backend/views/pages/currencies/show.traductions') }}
                                </h6>
                            </div>
                            <div class="card-body">

                                @forelse ($langs as $lang)
                                    <div class="border rounded p-3 mb-3">

                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="badge badge-soft-primary fs-13">{{ $lang->code }}</span>
                                            <span class="text-muted small">{{ $lang->name }}</span>
                                        </div>

                                        <div class="row mb-3 align-items-center">
                                            <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.nom') }}</div>
                                            <div class="col-9 fw-bold">{{ getTranslation($currency, $lang->id)?->name ?? '—' }}</div>
                                        </div>
                                        <hr class="my-2">

                                        <div class="row mb-3 align-items-center">
                                            <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.slug') }}</div>
                                            <div class="col-9"><code class="text-muted">{{ getTranslation($currency, $lang->id)?->slug ?? '—' }}</code></div>
                                        </div>
                                        <hr class="my-2">

                                        <div class="row mb-3 align-items-start">
                                            <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.description') }}</div>
                                            <div class="col-9">{{ getTranslation($currency, $lang->id)?->description ?? '—' }}</div>
                                        </div>
                                        <hr class="my-2">

                                        <div class="row align-items-center">
                                            <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.actions') }}</div>
                                            <div class="col-9">
                                                @if (getTranslation($currency, $lang->id))
                                                    <form action="{{ route('backend.currencies.translation.destroy', ['currencyTranslation' => getTranslation($currency, $lang->id)->uuid]) }}" method="POST" id="delete-form-{{ getTranslation($currency, $lang->id)->uuid }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmDelete('{{ getTranslation($currency, $lang->id)->uuid }}', '{{ __('v1/backend/views/pages/currencies/show.confirmerSuppressionTraduction') }}')">
                                                            <i class="bx bx-trash me-1"></i> {{ __('v1/backend/views/pages/currencies/show.supprimer') }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted small">{{ __('v1/backend/views/pages/currencies/show.aucuneTraductionDisponible') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                @empty
                                    <p class="text-muted mb-0">{{ __('v1/backend/views/pages/currencies/show.aucuneTraductionDisponible') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-trash me-2 text-warning"></i>{{ __('v1/backend/views/pages/currencies/show.corbeilleDesTraductions') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                @if ($currencyTranslations->isEmpty())
                                    <p class="text-muted mb-0">
                                        <i class="bx bx-archive me-1"></i>{{ __('v1/backend/views/pages/currencies/show.aucuneTraductionSupprimee') }}
                                    </p>
                                @else
                                    @include('v1.backend.pages.currencies.partials._trashed-table-translations', [
                                        'currencyTranslations' => $currencyTranslations,
                                        'currency' => $currency
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
                    <div class="col-md-8">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-data me-2 text-primary"></i>{{ __('v1/backend/views/pages/currencies/show.metadonnees') }}
                                </h6>
                            </div>
                            <div class="card-body">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.uuid') }}</div>
                                    <div class="col-8"><code>{{ $currency->uuid }}</code></div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.creeLe') }}</div>
                                    <div class="col-8">@formatDate($currency->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.modifieLe') }}</div>
                                    <div class="col-8">@formatDate($currency->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">

                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.supprimeLe') }}</div>
                                    <div class="col-8">
                                        @if ($currency->deleted_at)
                                            @formatDate($currency->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.currencies.partials._show-end', ['currency' => $currency])

@endsection

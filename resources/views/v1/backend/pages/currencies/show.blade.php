@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/currencies/show.pageTitle') }} — <code>{{ $currency->active_translation?->name ?? $currency->code }}</code>
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

            {{-- Informations générales --}}
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
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.tauxDeChange') }}</div>
                                    <div class="col-8">
                                        @if ($currency->exchange_rate !== null)
                                            <span class="badge badge-soft-info fs-13">{{ number_format((float) $currency->exchange_rate, 6, ',', ' ') }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.deviseParDefaut') }}</div>
                                    <div class="col-8">
                                        @if ($currency->is_default)
                                            <span class="badge badge-soft-success"><i class="bx bx-star me-1"></i>{{ __('v1/backend/views/pages/currencies/show.oui') }}</span>
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
                            <div class="card-body d-flex flex-column gap-3">

                                <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                     style="background: {{ $currency->status === \App\Enums\Currencies\CurrencyStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('v1/backend/views/pages/currencies/show.visibiliteDansApp') }}</div>
                                        <small class="text-muted">{{ __('v1/backend/views/pages/currencies/show.statut') }}</small>
                                    </div>
                                    @if ($currency->status === \App\Enums\Currencies\CurrencyStatus::Active)
                                        <span class="badge badge-soft-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i>{{ __('v1/backend/views/pages/currencies/show.actif') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-2">
                                            <i class="bx bx-x-circle me-1"></i>{{ __('v1/backend/views/pages/currencies/show.inactif') }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('backend.currencies.countries', ['currency' => $currency->uuid, 'slug' => $currency->active_translation?->slug ?? $currency->code]) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('v1/backend/views/pages/currencies/show.pays') }}</div>
                                        <small class="text-muted">{{ __('v1/backend/views/pages/currencies/show.paysUtilisantCetteDevise') }}</small>
                                    </div>
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="bx bx-map me-1"></i> {{ $currency->countries_count ?? 0 }}
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
                                    <i class="bx bx-globe me-2 text-primary"></i>{{ __('v1/backend/views/pages/currencies/show.traductions') }}
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
                                            <i class="bx bx-trash me-1 text-warning"></i> {{ __('v1/backend/views/pages/currencies/show.corbeille') }}
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
                                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.nom') }}</div>
                                                                <div class="col-9 fw-bold">{{ getTranslation($currency, $lang->id)?->name ?? '—' }}</div>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="row mb-3 align-items-center">
                                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.slug') }}</div>
                                                                <div class="col-9"><code>{{ getTranslation($currency, $lang->id)?->slug ?? '—' }}</code></div>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="row mb-3 align-items-start">
                                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.description') }}</div>
                                                                <div class="col-9">{{ getTranslation($currency, $lang->id)?->description ?? '—' }}</div>
                                                            </div>

                                                            @if(getTranslation($currency, $lang->id) && !getTranslation($currency, $lang->id)->trashed())
                                                                <hr class="my-2">
                                                                <div class="row align-items-start">
                                                                    <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/currencies/show.actions') }}</div>
                                                                    <div class="col-9">
                                                                        <form action="{{ route('backend.currencies.translation.destroy', ['currencyTranslation' => getTranslation($currency, $lang->id)->uuid]) }}" method="POST" id="delete-translation-form-{{ getTranslation($currency, $lang->id)->uuid }}" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-translation-form-{{ getTranslation($currency, $lang->id)->uuid }}', '{{ __('v1/backend/views/pages/currencies/show.confirmerSuppressionTraduction') }}', '{{ __('v1/backend/views/pages/currencies/show.supprimer') }}', 'warning')">
                                                                                <i class="bx bx-trash me-1"></i> {{ __('v1/backend/views/pages/currencies/show.supprimer') }}
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
                                                            <i class="bx bx-trash me-2 text-warning"></i>{{ __('v1/backend/views/pages/currencies/show.corbeilleDesTraductions') }}
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @include('v1.backend.pages.currencies.partials._trashed-table-translations', [
                                                            'currencyTranslations' => $currencyTranslations,
                                                            'currency' => $currency
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

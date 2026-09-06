@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/countries/show.pageTitle') }} — {{ $country->code }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/countries/show.pageTitle')])

    @include('v1.backend.pages.geos.countries.partials._show-start', ['country' => $country])

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> {{ __('v1/backend/views/pages/geos/countries/show.informationsGenerales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_translations">
                    <i class="bx bx-globe me-1"></i> {{ __('v1/backend/views/pages/geos/countries/show.traductions') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_meta">
                    <i class="bx bx-time-five me-1"></i> {{ __('v1/backend/views/pages/geos/countries/show.metadonnees') }}
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
                                    <i class="bx bx-file me-2 text-primary"></i>{{ __('v1/backend/views/pages/geos/countries/show.details') }}
                                </h6>
                            </div>
                            <div class="card-body">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.drapeau') }}</div>
                                    <div class="col-8">
                                        <a href="{{ asset(getEnvFolder() . $country->file_path) }}" ttarget="_blank" rrel="noopener noreferrer">
                                            <img src="{{ asset(getEnvFolder() . $country->file_path) }}" alt="{{ $country->code }}" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6;">
                                        </a>
                                    </div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.continent') }}</div>
                                    <div class="col-8 fw-semibold">
                                        @if($country->continent)
                                            <a href="{{ route('backend.geocontinents.show', ['geoContinent' => $country->continent->uuid, 'slug' => $country->continent->active_translation?->slug ?? 'null']) }}"
                                                class="fw-semibold text-primary text-decoration-none">
                                                {{ $country->continent->active_translation?->name ?? $country->continent->code }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.codeIso3') }}</div>
                                    <div class="col-8">
                                        <span class="badge bg-primary-subtle text-primary fs-13">{{ $country->code }}</span>
                                    </div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.codeIso2') }}</div>
                                    <div class="col-8">
                                        <span class="badge bg-secondary-subtle text-secondary fs-13">{{ $country->code_2 }}</span>
                                    </div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.indicatif') }}</div>
                                    <div class="col-8 fw-semibold">{{ $country->phone_code ?? '—' }}</div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.devise') }}</div>
                                    <div class="col-8 fw-semibold">
                                        @if ($country->currency_code)
                                            <span class="badge bg-success-subtle text-success fs-13">{{ $country->currency_symbol }} {{ $country->currency_code }}</span>
                                            <span class="text-muted ms-1 small">{{ $country->currency_name }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.type') }}</div>
                                    <div class="col-8">
                                        <span class="badge bg-info-subtle text-info fs-13">{{ $country->type_label }}</span>
                                    </div>
                                </div>
                                <hr class="my-2">

                                @if ($country->sovereign)
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.rattacheA') }}</div>
                                    <div class="col-8 fw-semibold">{{ $country->sovereign->active_translation?->name ?? $country->sovereign->code }}</div>
                                </div>
                                <hr class="my-2">
                                @endif

                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.ordre') }}</div>
                                    <div class="col-8">{{ $country->sort_order }}</div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-pulse me-2 text-primary"></i>{{ __('v1/backend/views/pages/geos/countries/show.statut') }}
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column gap-3">

                                <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                     style="background: {{ $country->status === \App\Enums\Geos\Countries\GeoCountryStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.activation') }}</div>
                                        <small class="text-muted">{{ __('v1/backend/views/pages/geos/countries/show.visibiliteDansApp') }}</small>
                                    </div>
                                    @if ($country->status === \App\Enums\Geos\Countries\GeoCountryStatus::Active)
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i>{{ __('v1/backend/views/pages/geos/countries/show.actif') }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                            <i class="bx bx-x-circle me-1"></i>{{ __('v1/backend/views/pages/geos/countries/show.inactif') }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('backend.geocountries.users', ['geoCountry' => $country->uuid, 'slug' => $country->active_translation?->slug ?? $country->code]) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded bg-light text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.utilisateurs') }}</div>
                                        <small class="text-muted">{{ __('v1/backend/views/pages/geos/countries/show.utilisateursDuPays') }}</small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 fs-13">
                                        <i class="bx bx-user me-1"></i>{{ $country->users_count ?? 0 }}
                                    </span>
                                </a>

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
                            <a class="nav-link {{ $index === 0 ? 'active' : '' }}"
                               data-bs-toggle="tab"
                               href="#trans_{{ $lang->code }}">
                                {{ $lang->name }}
                            </a>
                        </li>
                    @endforeach
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#trans_trash">
                            <i class="bx bx-trash me-1 text-warning"></i> {{ __('v1/backend/views/pages/geos/countries/show.corbeille') }}
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
                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.nom') }}</div>
                                                <div class="col-9 fw-bold">{{ getTranslation($country, $lang->id)?->name ?? '—' }}</div>
                                            </div>
                                            <hr class="my-2">

                                            <div class="row mb-3 align-items-center">
                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.slug') }}</div>
                                                <div class="col-9"><code class="text-muted">{{ getTranslation($country, $lang->id)?->slug ?? '—' }}</code></div>
                                            </div>
                                            <hr class="my-2">

                                            <div class="row mb-3 align-items-start">
                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.description') }}</div>
                                                <div class="col-9">{{ getTranslation($country, $lang->id)?->description ?? '—' }}</div>
                                            </div>
                                            <hr class="my-2">

                                            <div class="row align-items-center">
                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.actions') }}</div>
                                                <div class="col-9">
                                                    @if (getTranslation($country, $lang->id))
                                                        <form action="{{ route('backend.geocountries.translation.destroy', ['geoCountryTranslation' => getTranslation($country, $lang->id)->uuid]) }}" method="POST" id="delete-form-{{ getTranslation($country, $lang->id)->uuid }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ getTranslation($country, $lang->id)->uuid }}', '{{ __('v1/backend/views/pages/geos/countries/show.confirmerSuppressionTraduction') }}')">
                                                                <i class="bx bx-trash me-1"></i> {{ __('v1/backend/views/pages/geos/countries/show.supprimer') }}
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted small">{{ __('v1/backend/views/pages/geos/countries/show.aucuneTraductionDisponible') }}</span>
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
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-trash me-2 text-warning"></i>{{ __('v1/backend/views/pages/geos/countries/show.corbeilleDesTraductions') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                @if ($geoCountryTranslations->isEmpty())
                                    <p class="text-muted mb-0">
                                        <i class="bx bx-inbox me-1"></i>{{ __('v1/backend/views/pages/geos/countries/show.aucuneTraductionSupprimee') }}
                                    </p>
                                @else
                                    @include('v1.backend.pages.geos.countries.partials._trashed-table-translations', [
                                        'geoCountryTranslations' => $geoCountryTranslations,
                                        'country' => $country
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
                                    <i class="bx bx-data me-2 text-primary"></i>{{ __('v1/backend/views/pages/geos/countries/show.metadonnees') }}
                                </h6>
                            </div>
                            <div class="card-body">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.uuid') }}</div>
                                    <div class="col-8"><code>{{ $country->uuid }}</code></div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.creeLe') }}</div>
                                    <div class="col-8">@formatDate($country->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.modifieLe') }}</div>
                                    <div class="col-8">@formatDate($country->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">

                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/countries/show.supprimeLe') }}</div>
                                    <div class="col-8">
                                        @if ($country->deleted_at)
                                            @formatDate($country->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.geos.countries.partials._show-end', ['country' => $country])

@endsection
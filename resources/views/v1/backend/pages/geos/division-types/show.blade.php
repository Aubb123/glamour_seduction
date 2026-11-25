@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/division-types/show.pageTitle') }} — {{ $divisionType->code }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/division-types/show.pageTitle')])

    @include('v1.backend.pages.geos.division-types.partials._show-start', ['divisionType' => $divisionType])

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/show.informationsGenerales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_translations">
                    <i class="bx bx-globe me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/show.traductions') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_meta">
                    <i class="bx bx-time-five me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/show.metadonnees') }}
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
                                    <i class="bx bx-file me-2 text-primary"></i>{{ __('v1/backend/views/pages/geos/division-types/show.details') }}
                                </h6>
                            </div>
                            <div class="card-body">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/division-types/show.code') }}</div>
                                    <div class="col-8">
                                        <span class="badge badge-soft-primary fs-13">{{ $divisionType->code }}</span>
                                    </div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/division-types/show.niveau') }}</div>
                                    <div class="col-8">{{ $divisionType->level }}</div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/division-types/show.ordre') }}</div>
                                    <div class="col-8">{{ $divisionType->sort_order }}</div>
                                </div>
                                <hr class="my-2">

                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-pulse me-2 text-primary"></i>{{ __('v1/backend/views/pages/geos/division-types/show.statut') }}
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column gap-3">

                                <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                     style="background: {{ $divisionType->status === \App\Enums\Geos\DivisionTypes\GeoDivisionTypeStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('v1/backend/views/pages/geos/division-types/show.activation') }}</div>
                                        <small class="text-muted">{{ __('v1/backend/views/pages/geos/division-types/show.utilisableDansApp') }}</small>
                                    </div>
                                    @if ($divisionType->status === \App\Enums\Geos\DivisionTypes\GeoDivisionTypeStatus::Active)
                                        <span class="badge badge-soft-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/show.actif') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-2">
                                            <i class="bx bx-x-circle me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/show.inactif') }}
                                        </span>
                                    @endif
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
                            <a class="nav-link {{ $index === 0 ? 'active' : '' }}"
                               data-bs-toggle="tab"
                               href="#trans_{{ $lang->code }}">
                                {{ $lang->name }}
                            </a>
                        </li>
                    @endforeach
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#trans_trash">
                            <i class="bx bx-trash me-1 text-warning"></i> {{ __('v1/backend/views/pages/geos/division-types/show.corbeille') }}
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
                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/division-types/show.libelle') }}</div>
                                                <div class="col-9 fw-bold">{{ getTranslation($divisionType, $lang->id)?->name ?? '—' }}</div>
                                            </div>
                                            <hr class="my-2">

                                            <div class="row mb-3 align-items-center">
                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/division-types/show.slug') }}</div>
                                                <div class="col-9"><code class="text-muted">{{ getTranslation($divisionType, $lang->id)?->slug ?? '—' }}</code></div>
                                            </div>
                                            <hr class="my-2">

                                            <div class="row mb-3 align-items-start">
                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/division-types/show.description') }}</div>
                                                <div class="col-9">{{ getTranslation($divisionType, $lang->id)?->description ?? '—' }}</div>
                                            </div>
                                            <hr class="my-2">

                                            <div class="row align-items-center">
                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/division-types/show.actions') }}</div>
                                                <div class="col-9">
                                                    @if (getTranslation($divisionType, $lang->id))
                                                        <form action="{{ route('backend.geodivisiontypes.translation.destroy', ['geoDivisionTypeTranslation' => getTranslation($divisionType, $lang->id)->uuid]) }}" method="POST" id="delete-form-{{ getTranslation($divisionType, $lang->id)->uuid }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmDelete('{{ getTranslation($divisionType, $lang->id)->uuid }}', '{{ __('v1/backend/views/pages/geos/division-types/show.confirmerSuppressionTraduction') }}')">
                                                                <i class="bx bx-trash me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/show.supprimer') }}
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted small">{{ __('v1/backend/views/pages/geos/division-types/show.aucuneTraductionDisponible') }}</span>
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
                                    <i class="bx bx-trash me-2 text-warning"></i> {{ __('v1/backend/views/pages/geos/division-types/show.corbeilleDesTraductions') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                @if ($geoDivisionTypeTranslations->isEmpty())
                                    <p class="text-muted mb-0">
                                        <i class="bx bx-archive me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/show.aucuneTraductionSupprimee') }}
                                    </p>
                                @else
                                    @include('v1.backend.pages.geos.division-types.partials._trashed-table-translations', [
                                        'divisionTypeTranslations' => $geoDivisionTypeTranslations,
                                        'divisionType' => $divisionType
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
                                    <i class="bx bx-data me-2 text-primary"></i> {{ __('v1/backend/views/pages/geos/division-types/show.metadonnees') }}
                                </h6>
                            </div>
                            <div class="card-body">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/division-types/show.uuid') }}</div>
                                    <div class="col-8"><code>{{ $divisionType->uuid }}</code></div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/division-types/show.creeLe') }}</div>
                                    <div class="col-8">@formatDate($divisionType->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/division-types/show.modifieLe') }}</div>
                                    <div class="col-8">@formatDate($divisionType->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">

                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/division-types/show.supprimeLe') }}</div>
                                    <div class="col-8">
                                        @if ($divisionType->deleted_at)
                                            @formatDate($divisionType->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.geos.division-types.partials._show-end', ['divisionType' => $divisionType])

@endsection


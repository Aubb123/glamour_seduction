@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/divisions/show.pageTitle') }} — {{ $division->active_translation?->name ?? '' }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/divisions/show.pageTitle')])

    @include('v1.backend.pages.geos.divisions.partials._show-start', ['division' => $division])

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/show.informationsGenerales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_translations">
                    <i class="bx bx-globe me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/show.traductions') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_meta">
                    <i class="bx bx-time-five me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/show.metadonnees') }}
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
                                    <i class="bx bx-info-circle me-2 text-primary"></i>{{ __('v1/backend/views/pages/geos/divisions/show.details') }}
                                </h6>
                            </div>
                            <div class="card-body">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.pays') }}</div>
                                    <div class="col-8">{{ $division->country?->active_translation?->name ?? $division->country?->code ?? '—' }}</div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.typeDeDivision') }}</div>
                                    <div class="col-8">{{ $division->type?->active_translation?->name ?? $division->type?->code ?? '—' }}</div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.parent') }}</div>
                                    <div class="col-8">{{ $division->parent?->active_translation?->name ?? __('v1/backend/views/pages/geos/divisions/show.aucunParent') }}</div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.niveau') }}</div>
                                    <div class="col-8 fw-semibold">{{ $division->level }}</div>
                                </div>
                                <hr class="my-2">

                                <div class="row align-items-start">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.notes') }}</div>
                                    <div class="col-8">{{ $division->notes ?? __('v1/backend/views/pages/geos/divisions/show.aucuneNote') }}</div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-pulse me-2 text-primary"></i>{{ __('v1/backend/views/pages/geos/divisions/show.statut') }}
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column gap-3">

                                <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                     style="background: {{ $division->status === \App\Enums\Geos\Divisions\GeoDivisionStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.activation') }}</div>
                                        <small class="text-muted">{{ __('v1/backend/views/pages/geos/divisions/show.utilisableDansApp') }}</small>
                                    </div>
                                    @if ($division->status === \App\Enums\Geos\Divisions\GeoDivisionStatus::Active)
                                        <span class="badge badge-soft-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/show.actif') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-2">
                                            <i class="bx bx-x-circle me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/show.inactif') }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('backend.geodivisions.children', ['geoDivision' => $division->uuid, 'slug' => $division->active_translation?->slug ?? 'null']) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.sousDivisions') }}</div>
                                        <small class="text-muted">{{ __('v1/backend/views/pages/geos/divisions/show.sousDivisionsAide') }}</small>
                                    </div>
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="bx bx-sitemap me-1"></i> {{ $division->children_count ?? 0 }} {{ __('v1/backend/views/pages/geos/divisions/show.entrees') }}
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
                            <i class="bx bx-trash me-1 text-warning"></i> {{ __('v1/backend/views/pages/geos/divisions/show.corbeille') }}
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
                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.nom') }}</div>
                                                <div class="col-9 fw-bold">{{ getTranslation($division, $lang->id)?->name ?? '—' }}</div>
                                            </div>
                                            <hr class="my-2">

                                            <div class="row mb-3 align-items-center">
                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.slug') }}</div>
                                                <div class="col-9"><code class="text-muted">{{ getTranslation($division, $lang->id)?->slug ?? '—' }}</code></div>
                                            </div>
                                            <hr class="my-2">

                                            <div class="row mb-3 align-items-start">
                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.description') }}</div>
                                                <div class="col-9">{{ getTranslation($division, $lang->id)?->description ?? '—' }}</div>
                                            </div>
                                            <hr class="my-2">

                                            <div class="row align-items-center">
                                                <div class="col-3 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.actions') }}</div>
                                                <div class="col-9">
                                                    @if (getTranslation($division, $lang->id))
                                                        <form action="{{ route('backend.geodivisions.translation.destroy', ['geoDivisionTranslation' => getTranslation($division, $lang->id)->uuid]) }}" method="POST" id="delete-form-{{ getTranslation($division, $lang->id)->uuid }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmDelete('{{ getTranslation($division, $lang->id)->uuid }}', '{{ __('v1/backend/views/pages/geos/divisions/show.confirmerSuppressionTraduction') }}')">
                                                                <i class="bx bx-trash me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/show.supprimer') }}
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted small">{{ __('v1/backend/views/pages/geos/divisions/show.aucuneTraductionDisponible') }}</span>
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
                                    <i class="bx bx-trash me-2 text-warning"></i> {{ __('v1/backend/views/pages/geos/divisions/show.corbeilleDesTraductions') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                @if ($geoDivisionTranslations->isEmpty())
                                    <p class="text-muted mb-0">
                                        <i class="bx bx-archive me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/show.aucuneTraductionSupprimee') }}
                                    </p>
                                @else
                                    @include('v1.backend.pages.geos.divisions.partials._trashed-table-translations', [
                                        'geoDivisionTranslations' => $geoDivisionTranslations,
                                        'division' => $division
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
                                    <i class="bx bx-data me-2 text-primary"></i> {{ __('v1/backend/views/pages/geos/divisions/show.metadonnees') }}
                                </h6>
                            </div>
                            <div class="card-body">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.uuid') }}</div>
                                    <div class="col-8"><code>{{ $division->uuid }}</code></div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.creeLe') }}</div>
                                    <div class="col-8">@formatDate($division->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.modifieLe') }}</div>
                                    <div class="col-8">@formatDate($division->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">

                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/geos/divisions/show.supprimeLe') }}</div>
                                    <div class="col-8">
                                        @if ($division->deleted_at)
                                            @formatDate($division->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.geos.divisions.partials._show-end', ['division' => $division])

@endsection
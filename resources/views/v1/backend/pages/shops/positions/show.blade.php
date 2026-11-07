@extends('v1.backend.layouts.app')

@section('title')
    Détail du poste — <code>{{ $position->active_translation?->name ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Détail du poste'])

    @include('v1.backend.pages.shops.positions.partials._show-start', ['position' => $position])

        @php
            // Repli : mêmes données que blog_tags (langues actives + traductions en corbeille)
            $langs = $langs ?? getActiveLangs()['langs'];
            $positionTranslations = $positionTranslations ?? \App\Models\PositionTranslation::onlyTrashed()->where('position_id', $position->id)->get();
        @endphp

        {{-- Onglets internes (design blog_tags / shops) --}}
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> Informations générales
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_translations">
                    <i class="bx bx-globe me-1"></i> Traductions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_meta">
                    <i class="bx bx-time-five me-1"></i> Métadonnées
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
                                    <i class="bx bx-info-circle me-2 text-primary"></i>Détails
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">Nom</div>
                                    <div class="col-8 fw-semibold">{{ $position->active_translation?->name ?? '—' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">Code</div>
                                    <div class="col-8"><code>{{ $position->code }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">Portée</div>
                                    <div class="col-8"><span class="{{ $position->context?->badgeClass() }}">{{ $position->context?->label() }}</span></div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">Description</div>
                                    <div class="col-8">{{ $position->active_translation?->description ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-pulse me-2 text-primary"></i>Statut
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column gap-3">

                                <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                     style="background: {{ $position->status === \App\Enums\Shops\Positions\PositionStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">Activation</div>
                                        <small class="text-muted">Visibilité interne</small>
                                    </div>
                                    @if ($position->status === \App\Enums\Shops\Positions\PositionStatus::Active)
                                        <span class="badge badge-soft-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i>Actif
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-2">
                                            <i class="bx bx-x-circle me-1"></i>Inactif
                                        </span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: #f4f5f7">
                                    <div>
                                        <div class="fw-semibold">Personnel boutique</div>
                                        <small class="text-muted">Affectations en boutique</small>
                                    </div>
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="bx bx-group me-1"></i> {{ $position->shop_users_count ?? 0 }}
                                    </span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: #f4f5f7">
                                    <div>
                                        <div class="fw-semibold">Personnel entrepôt</div>
                                        <small class="text-muted">Affectations en entrepôt</small>
                                    </div>
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="bx bx-building-house me-1"></i> {{ $position->warehouse_users_count ?? 0 }}
                                    </span>
                                </div>

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
                                    <i class="bx bx-globe me-2 text-primary"></i>Traductions
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
                                            <i class="bx bx-trash me-1 text-warning"></i> Corbeille
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    @foreach ($langs as $index => $lang)
                                        @php $translation = $position->translations->firstWhere('lang_id', $lang->id); @endphp
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
                                                                <div class="col-3 text-muted fw-semibold">Nom</div>
                                                                <div class="col-9 fw-bold">{{ $translation?->name ?? '—' }}</div>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="row mb-3 align-items-center">
                                                                <div class="col-3 text-muted fw-semibold">Slug</div>
                                                                <div class="col-9"><code>{{ $translation?->slug ?? '—' }}</code></div>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="row align-items-start">
                                                                <div class="col-3 text-muted fw-semibold">Description</div>
                                                                <div class="col-9">{{ $translation?->description ?? '—' }}</div>
                                                            </div>

                                                            @if($translation && !$translation->trashed())
                                                                <hr class="my-2">
                                                                <div class="row align-items-start">
                                                                    <div class="col-3 text-muted fw-semibold">Actions</div>
                                                                    <div class="col-9">
                                                                        <form action="{{ route('backend.position-translations.destroy', ['uuid' => $translation->uuid]) }}" method="POST" id="delete-translation-form-{{ $translation->uuid }}" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-translation-form-{{ $translation->uuid }}', '{{ __('Supprimer cette traduction ?') }}', 'Supprimer', 'warning')">
                                                                                <i class="bx bx-trash me-1"></i> Supprimer
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
                                                @include('v1.backend.pages.shops.positions.partials._trashed-table-translations', [
                                                    'positionTranslations' => $positionTranslations,
                                                    'position' => $position
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
                                    <div class="col-4 text-muted fw-semibold">UUID</div>
                                    <div class="col-8"><code>{{ $position->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">Créé le</div>
                                    <div class="col-8">@formatDate($position->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">Modifié le</div>
                                    <div class="col-8">@formatDate($position->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">Supprimé le</div>
                                    <div class="col-8">
                                        @if ($position->deleted_at)
                                            @formatDate($position->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.shops.positions.partials._show-end', ['position' => $position])

@endsection

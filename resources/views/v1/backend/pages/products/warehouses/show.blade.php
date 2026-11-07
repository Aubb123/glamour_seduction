@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'entrepôt') }} — <code>{{ $warehouse->active_translation?->name ?? $warehouse->code }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de l\'entrepôt')])

    @include('v1.backend.pages.products.warehouses.partials._show-start', ['warehouse' => $warehouse])

        @php($translation = $warehouse->active_translation)

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> {{ __('Informations générales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_translations">
                    <i class="bx bx-globe me-1"></i> {{ __('Traductions') }}
                    @if($warehouse->translations->isNotEmpty())
                        <span class="badge bg-primary ms-1">{{ $warehouse->translations->count() }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_staff">
                    <i class="bx bx-group me-1"></i> {{ __('Personnel') }}
                    @if($warehouse->warehouseUsers->isNotEmpty())
                        <span class="badge bg-primary ms-1">{{ $warehouse->warehouseUsers->count() }}</span>
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
                                    <i class="bx bx-file me-2 text-primary"></i>{{ __('Détails') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code>{{ $warehouse->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Code') }}</div>
                                    <div class="col-8"><code>{{ $warehouse->code }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Nom') }}</div>
                                    <div class="col-8 {{-- display name --}}">{{ $translation?->name ?? '—' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Adresse') }}</div>
                                    <div class="col-8">{{ $translation?->address ?? '—' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Ville') }}</div>
                                    <div class="col-8">{{ $translation?->city ?? '—' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Téléphone') }}</div>
                                    <div class="col-8">{{ $translation?->phone ?? '—' }}</div>
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
                                <span class="{{ $warehouse->status?->badgeClass() ?? 'badge badge-soft-secondary' }} fs-14 px-3 py-2">
                                    {{ $warehouse->status?->label() ?? '—' }}
                                </span>
                                <hr>
                                <span class="badge badge-soft-info">
                                    <i class="bx bx-store me-1"></i>{{ $warehouse->shops_count ?? 0 }} {{ __('boutique(s) approvisionnée(s)') }}
                                </span>
                                <hr>
                                <span class="badge badge-soft-primary">
                                    <i class="bx bx-group me-1"></i>{{ $warehouse->warehouse_users_count ?? $warehouse->warehouseUsers->count() }} {{ __('membre(s) du personnel') }}
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Traductions -->
            <div class="tab-pane fade" id="tab_translations">
                <div class="row g-4">
                    @forelse ($warehouse->translations as $t)
                        <div class="col-md-6">
                            <div class="card border h-100">
                                <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bx bx-globe me-2 text-primary"></i>{{ $t->lang?->name ?? $t->lang?->code ?? '—' }}
                                    </h6>
                                    <span class="badge badge-soft-primary">{{ $t->lang?->code ?? '—' }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Nom') }}</div>
                                        <div class="col-8 fw-semibold">{{ $t->name ?? '—' }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">Slug</div>
                                        <div class="col-8"><code>{{ $t->slug }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Adresse') }}</div>
                                        <div class="col-8">{{ $t->address ?? '—' }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Ville') }}</div>
                                        <div class="col-8">{{ $t->city ?? '—' }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Téléphone') }}</div>
                                        <div class="col-8">{{ $t->phone ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning mb-0">{{ __('Aucune traduction trouvée.') }}</div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Personnel -->
            <div class="tab-pane fade" id="tab_staff">
                @include('v1.backend.pages.products.warehouses.partials._show-staff', ['warehouse' => $warehouse])
            </div>

            <!-- Métadonnées -->
            <div class="tab-pane fade" id="tab_meta">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-time-five me-2 text-primary"></i>{{ __('Métadonnées') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($warehouse->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                    <div class="col-8">@formatDate($warehouse->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                @if($warehouse->deleted_at)
                                    <hr class="my-2">
                                    <div class="row align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                        <div class="col-8">@formatDate($warehouse->deleted_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @include('v1.backend.pages.products.warehouses.partials._show-end', ['warehouse' => $warehouse])

@endsection

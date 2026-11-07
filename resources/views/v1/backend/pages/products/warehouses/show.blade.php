@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'entrepôt') }} — <code>{{ $warehouse->active_translation?->name ?? $warehouse->code }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de l\'entrepôt')])

    @include('v1.backend.pages.products.warehouses.partials._show-start', ['warehouse' => $warehouse])

        @php($translation = $warehouse->active_translation)
        @php($langs = $langs ?? getActiveLangs()['langs'])
        @php($warehouseTranslations = $warehouseTranslations ?? \App\Models\ProductWarehouseTranslation::onlyTrashed()->where('warehouse_id', $warehouse->id)->get())

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
                                    <div class="col-8 fw-semibold">{{ $translation?->name ?? '—' }}</div>
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
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-globe me-2 text-primary"></i>{{ __('Traductions') }}
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
                                            <i class="bx bx-trash me-1 text-warning"></i> {{ __('Corbeille') }}
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    @foreach ($langs as $index => $lang)
                                        @php($langTranslation = getTranslation($warehouse, $lang->id))
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
                                                                <div class="col-3 text-muted fw-semibold">{{ __('Nom') }}</div>
                                                                <div class="col-9 fw-bold">{{ $langTranslation?->name ?? '—' }}</div>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="row mb-3 align-items-center">
                                                                <div class="col-3 text-muted fw-semibold">{{ __('Slug') }}</div>
                                                                <div class="col-9"><code>{{ $langTranslation?->slug ?? '—' }}</code></div>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="row mb-3 align-items-center">
                                                                <div class="col-3 text-muted fw-semibold">{{ __('Adresse') }}</div>
                                                                <div class="col-9">{{ $langTranslation?->address ?? '—' }}</div>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="row mb-3 align-items-center">
                                                                <div class="col-3 text-muted fw-semibold">{{ __('Ville') }}</div>
                                                                <div class="col-9">{{ $langTranslation?->city ?? '—' }}</div>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="row mb-3 align-items-center">
                                                                <div class="col-3 text-muted fw-semibold">{{ __('Téléphone') }}</div>
                                                                <div class="col-9">{{ $langTranslation?->phone ?? '—' }}</div>
                                                            </div>

                                                            @if($langTranslation && !$langTranslation->trashed())
                                                                <hr class="my-2">
                                                                <div class="row align-items-start">
                                                                    <div class="col-3 text-muted fw-semibold">{{ __('Actions') }}</div>
                                                                    <div class="col-9">
                                                                        <form action="{{ route('backend.product-warehouse-translations.destroy', ['uuid' => $langTranslation->uuid]) }}" method="POST" id="delete-translation-form-{{ $langTranslation->uuid }}" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-translation-form-{{ $langTranslation->uuid }}', '{{ __('Supprimer cette traduction ?') }}', 'Supprimer', 'warning')">
                                                                                <i class="bx bx-trash me-1"></i> {{ __('Supprimer') }}
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
                                                            <i class="bx bx-trash me-2 text-warning"></i>{{ __('Corbeille des traductions') }}
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if ($warehouseTranslations->isEmpty())
                                                            <p class="text-muted mb-0">{{ __('Aucune traduction supprimée.') }}</p>
                                                        @else
                                                            @include('v1.backend.pages.products.warehouses.partials._trashed-table-translations', [
                                                                'trashedTranslations' => $warehouseTranslations,
                                                                'warehouse' => $warehouse,
                                                            ])
                                                        @endif
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

            <!-- Personnel -->
            <div class="tab-pane fade" id="tab_staff">
                @include('v1.backend.pages.products.warehouses.partials._show-staff', ['warehouse' => $warehouse])
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
                                    <div class="col-8"><code>{{ $warehouse->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($warehouse->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                    <div class="col-8">@formatDate($warehouse->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                    <div class="col-8">
                                        @if ($warehouse->deleted_at)
                                            @formatDate($warehouse->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.products.warehouses.partials._show-end', ['warehouse' => $warehouse])

@endsection

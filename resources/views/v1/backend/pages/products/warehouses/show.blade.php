@extends('v1.backend.layouts.app')

@section('title')
    {{ __("Détail de l'entrepôt") }} — <code>{{ $warehouse->active_translation?->name ?? $warehouse->code }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __("Détail de l'entrepôt")])

    @include('v1.backend.pages.products.warehouses.partials._show-start', ['warehouse' => $warehouse])

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

            {{-- Informations générales --}}
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
                                    <div class="col-4 text-muted fw-semibold">{{ __('Code') }}</div>
                                    <div class="col-8"><code>{{ $warehouse->code }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Nom') }}</div>
                                    <div class="col-8 fw-bold">{{ $warehouse->active_translation?->name ?? '—' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Ordre de tri') }}</div>
                                    <div class="col-8 fw-semibold">{{ $warehouse->sort_order }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Type') }}</div>
                                    <div class="col-8">
                                        <span class="{{ $warehouse->type?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $warehouse->type?->label() ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Mode de suivi du stock') }}</div>
                                    <div class="col-8">
                                        <span class="{{ $warehouse->stock_tracking_mode?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $warehouse->stock_tracking_mode?->label() ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Ville') }}</div>
                                    <div class="col-8">{{ $warehouse->city ?? '—' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Division') }}</div>
                                    <div class="col-8">
                                        @if($warehouse->division)
                                            <a href="{{ route('backend.geodivisions.show', ['geoDivision' => $warehouse->division->uuid, 'slug' => $warehouse->division->active_translation?->slug ?? 'null']) }}" class="text-decoration-none">
                                                {{ $warehouse->division->active_translation?->name ?? '—' }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Adresse') }}</div>
                                    <div class="col-8">{{ $warehouse->address ?? '—' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Coordonnées GPS') }}</div>
                                    <div class="col-8">
                                        @if ($warehouse->latitude !== null && $warehouse->longitude !== null)
                                            <code>{{ $warehouse->latitude }}, {{ $warehouse->longitude }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
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
                                        style="background: {{ $warehouse->status === \App\Enums\Products\Warehouses\ProductWarehouseStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('Statut') }}</div>
                                        <small class="text-muted">
                                            {{ $warehouse->status === \App\Enums\Products\Warehouses\ProductWarehouseStatus::Active ? __('Entrepôt actif') : __('Entrepôt inactif') }}
                                        </small>
                                    </div>
                                    <span class="{{ $warehouse->status?->badgeClass() ?? 'badge badge-soft-secondary' }} px-3 py-2">
                                        {{ $warehouse->status?->label() ?? '—' }}
                                    </span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: {{ $warehouse->is_default ? '#fff8e5' : '#f5f5f5' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('Entrepôt par défaut') }}</div>
                                        <small class="text-muted">{{ $warehouse->is_default ? __('Utilisé par défaut pour les opérations') : __('Non défini comme défaut') }}</small>
                                    </div>
                                    @if ($warehouse->is_default)
                                        <span class="badge badge-soft-warning px-3 py-2"><i class="bx bx-star me-1"></i> {{ __('Défaut') }}</span>
                                    @else
                                        <span class="badge badge-soft-secondary px-3 py-2">—</span>
                                    @endif
                                </div>

                                <div class="p-3 rounded" style="background: #f5f7fa">
                                    <div class="fw-semibold mb-1">{{ __('Contact') }}</div>
                                    <div class="small text-muted">{{ $warehouse->phone ?? '—' }}</div>
                                    <div class="small text-muted">{{ $warehouse->email ?? '—' }}</div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                @if ($warehouse->notes)
                    <div class="card border mt-4">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 fw-bold"><i class="bx bx-note me-2 text-primary"></i>{{ __('Notes internes') }}</h6>
                        </div>
                        <div class="card-body">
                            {!! nl2br(e($warehouse->notes)) !!}
                        </div>
                    </div>
                @endif
            </div>


            {{-- Traductions --}}
            <div class="tab-pane fade" id="tab_translations">

                <ul class="nav nav-pills mb-3">
                    @foreach ($langs as $index => $lang)
                        <li class="nav-item">
                            <a class="nav-link @if($index === 0) active @endif" data-bs-toggle="tab" href="#trans_lang_{{ $lang->code }}">{{ $lang->name }}</a>
                        </li>
                    @endforeach

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#trans_trash">
                            <i class="bx bx-trash me-1"></i> {{ __('Corbeille') }}
                            <span class="badge bg-warning ms-1">{{ $productWarehouseTranslations->count() }}</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    @foreach ($langs as $index => $lang)
                        @php($translation = getTranslation($warehouse, $lang->id))
                        <div class="tab-pane fade @if($index === 0) show active @endif" id="trans_lang_{{ $lang->code }}">
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
                                                <div class="col-2 text-muted fw-semibold">{{ __('Nom') }}</div>
                                                <div class="col-9 fw-bold">{{ $translation?->name ?? '—' }}</div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-2 text-muted fw-semibold">{{ __('Slug') }}</div>
                                                <div class="col-9"><code>{{ $translation?->slug ?? '—' }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-start">
                                                <div class="col-2 text-muted fw-semibold">{{ __('Description') }}</div>
                                                <div class="col-9">
                                                    @if ($translation?->description)
                                                        {!! nl2br(e($translation->description)) !!}
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <hr class="my-2">

                                            <div class="row align-items-start">
                                                <div class="col-2 text-muted fw-semibold">{{ __('Actions') }}</div>
                                                <div class="col-9">
                                                    @if($translation && ! $translation->trashed())
                                                        <form action="{{ route('backend.product-warehouse-translations.destroy', ['productWarehouseTranslation' => $translation->uuid]) }}" method="POST" id="delete-form-{{ $translation->uuid }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $translation->uuid }}', 'Supprimer cette traduction ?', 'Supprimer', 'warning')">
                                                                <i class="bx bx-trash me-1"></i> Supprimer
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
                                        @if ($productWarehouseTranslations->isEmpty())
                                            <p class="text-muted mb-0">{{ __('Aucune traduction supprimée.') }}</p>
                                        @else
                                            @include('v1.backend.pages.products.warehouses.partials._trashed-table-translations', [
                                                'productWarehouseTranslations' => $productWarehouseTranslations,
                                                'warehouse' => $warehouse
                                            ])
                                        @endif
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


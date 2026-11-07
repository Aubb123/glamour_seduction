@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du mouvement de stock') }} — {{ $movement->type->label() }} {{ $movement->quantity }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail du mouvement de stock')])

    @include('v1.backend.pages.products.stock-movements.partials._show-start', ['movement' => $movement])

        @include('v1.backend.pages.products.stock-movements.partials._show-navigation', ['movement' => $movement])

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
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code>{{ $movement->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Entrepôt') }}</div>
                                    <div class="col-8 fw-semibold">
                                        @if ($movement->warehouse)
                                            {{ $movement->warehouse->name }}
                                            <span class="badge badge-soft-primary ms-1">{{ $movement->warehouse->code }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Produit') }}</div>
                                    <div class="col-8 fw-semibold">{{ $movement->variant?->product?->active_translation?->name ?? '—' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Variante (SKU)') }}</div>
                                    <div class="col-8"><code>{{ $movement->variant?->sku ?? '—' }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Référence') }}</div>
                                    <div class="col-8">
                                        @if ($movement->reference_type)
                                            {{ $movement->reference_type }} #{{ $movement->reference_id }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Enregistré par') }}</div>
                                    <div class="col-8 fw-semibold">
                                        @php $reporter = $movement->createdBy; @endphp
                                        {{ $reporter ? $reporter->first_name.' '.$reporter->last_name : '—' }}
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-start">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Note') }}</div>
                                    <div class="col-8">{{ $movement->note ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-transfer me-2 text-primary"></i>{{ __('Mouvement') }}
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column gap-3">

                                <div class="d-flex align-items-center justify-content-between p-3 rounded">
                                    <div>
                                        <div class="fw-semibold">{{ __('Type') }}</div>
                                        <small class="text-muted">{{ __('Sens du mouvement') }}</small>
                                    </div>
                                    <span class="{{ $movement->type->badgeClass() }} px-3 py-2">
                                        {{ $movement->type->label() }}
                                    </span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                     style="background: {{ $movement->type->decreasesStock() ? '#fef0f0' : '#e8f9f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('Quantité') }}</div>
                                        <small class="text-muted">{{ $movement->type->decreasesStock() ? __('Diminue le stock') : __('Augmente le stock') }}</small>
                                    </div>
                                    <span class="fs-4 fw-bold {{ $movement->type->decreasesStock() ? 'text-danger' : 'text-success' }}">
                                        {{ $movement->type->decreasesStock() ? '−' : '+' }}{{ $movement->quantity }}
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Métadonnées --}}
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
                                    <div class="col-4 text-muted fw-semibold">{{ __('Identifiant') }}</div>
                                    <div class="col-8"><code>#{{ $movement->id }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($movement->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Mis à jour le') }}</div>
                                    <div class="col-8">@formatDate($movement->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                    <div class="col-8">
                                        @if ($movement->deleted_at)
                                            @formatDate($movement->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.products.stock-movements.partials._show-end', ['movement' => $movement])

@endsection

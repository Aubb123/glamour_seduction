@extends('v1.backend.layouts.app')

@section('title')
    Détail de la variante — <code>{{ $variant->sku }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Détail de la variante'])

    @include('v1.backend.pages.products.variants.partials._show-start', ['variant' => $variant])

        <div class="row g-4">
            @php($defaultCurrency = \App\Models\Currency::where('is_default', true)->first())

            <div class="col-md-8">
                <div class="card border h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="bx bx-info-circle me-2 text-primary"></i>Détails
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Produit</div>
                            <div class="col-8">
                                <a href="{{ route('backend.products.show', ['product' => $variant->product?->uuid, 'slug' => $variant->product?->active_translation?->slug ?? 'null']) }}"
                                   class="badge badge-soft-primary text-decoration-none">
                                    {{ $variant->product?->active_translation?->name ?? ('#'.$variant->product_id) }}
                                </a>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">SKU</div>
                            <div class="col-8"><code>{{ $variant->sku }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Code-barres</div>
                            <div class="col-8">{{ $variant->barcode ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Prix</div>
                            <div class="col-8 fw-bold">
                                @if ($variant->price)
                                    {{ number_format((float) $variant->price, 2, ',', ' ') }}{{ $defaultCurrency?->symbol ? ' '.$defaultCurrency->symbol : '' }}
                                    @if ($defaultCurrency)
                                        <span class="text-muted fw-normal small ms-1">(devise par défaut : {{ $defaultCurrency->active_translation?->name ?? $defaultCurrency->code }})</span>
                                    @endif
                                @else
                                    <span class="text-muted">Hérite du produit ({{ number_format((float) ($variant->product?->base_price ?? 0), 2, ',', ' ') }}{{ $defaultCurrency?->symbol ? ' '.$defaultCurrency->symbol : '' }})</span>
                                @endif
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">UUID</div>
                            <div class="col-8"><code>{{ $variant->uuid }}</code></div>
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
                    <div class="card-body text-center">
                        <span class="badge {{ $variant->status->badgeClass() }} fs-14 px-3 py-2">
                            {{ $variant->status->label() }}
                        </span>
                        <hr>
                        @if ($variant->is_default)
                            <span class="badge badge-soft-success"><i class="bx bxs-star me-1"></i>Variante par défaut</span>
                        @else
                            <span class="badge badge-soft-secondary">Variante secondaire</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card border">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="bx bx-layer me-2 text-primary"></i>Valeurs d'attributs
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @forelse ($variant->attributeValues as $value)
                                <span class="badge badge-soft-primary fs-14 px-3 py-2">
                                    {{ $value->attribute?->active_translation?->name ?? '?' }} : {{ $value->active_translation?->label ?? $value->value }}
                                </span>
                            @empty
                                <span class="text-muted">Aucune valeur d'attribut rattachée.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @include('v1.backend.pages.products.variants.partials._show-end', ['variant' => $variant])

@endsection

@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la variante') }} — <code>{{ $variant->sku }}</code>
@endsection

@section('content')
    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de la variante')])
    @include('v1.backend.pages.products.variants.partials._show-start', ['variant' => $variant])

    @php
        $defaultCurrency = \App\Models\Currency::where('is_default', true)->first();
        $variantImages = $variant->cloudFiles->where('file_type', 'image')->sortBy(fn ($file) => $file->metadata['page'] ?? 0)->values();
    @endphp

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab_general"><i class="bx bx-info-circle me-1"></i> {{ __('Informations générales') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_attributes"><i class="bx bx-layer me-1"></i> {{ __("Valeurs d'attributs") }} @if($variant->attributeValues->isNotEmpty()) <span class="badge bg-primary ms-1">{{ $variant->attributeValues->count() }}</span> @endif</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_images"><i class="bx bx-image me-1"></i> {{ __('Images') }} <span class="badge bg-primary ms-1">{{ $variantImages->count() }}</span></a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_meta"><i class="bx bx-time-five me-1"></i> {{ __('Métadonnées') }}</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="tab_general">
            <div class="row g-4">
                <div class="col-md-8"><div class="card border h-100"><div class="card-header bg-light py-2"><h6 class="mb-0 fw-bold"><i class="bx bx-file me-2 text-primary"></i>{{ __('Détails') }}</h6></div><div class="card-body">
                    <div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('Produit parent') }}</div><div class="col-8"><a href="{{ route('backend.products.show', ['product' => $variant->product?->uuid, 'slug' => $variant->product?->active_translation?->slug ?? 'null']) }}" class="badge badge-soft-primary text-decoration-none">{{ $variant->product?->active_translation?->name ?? ('#'.$variant->product_id) }}</a></div></div><hr class="my-2">
                    <div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div><div class="col-8"><code>{{ $variant->uuid }}</code></div></div><hr class="my-2">
                    <div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">SKU</div><div class="col-8"><code>{{ $variant->sku }}</code></div></div><hr class="my-2">
                    <div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('Code-barres') }}</div><div class="col-8">{{ $variant->barcode ?? '—' }}</div></div><hr class="my-2">
                    <div class="row align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('Prix') }}</div><div class="col-8 fw-bold">@if ($variant->price) {{ number_format((float) $variant->price, 2, ',', ' ') }}{{ $defaultCurrency?->symbol ? ' '.$defaultCurrency->symbol : '' }} @if ($defaultCurrency) <span class="text-muted fw-normal small ms-1">({{ __('devise par défaut') }} : {{ $defaultCurrency->active_translation?->name ?? $defaultCurrency->code }})</span> @endif @else <span class="text-muted">{{ __('Hérite du produit') }} ({{ number_format((float) ($variant->product?->base_price ?? 0), 2, ',', ' ') }}{{ $defaultCurrency?->symbol ? ' '.$defaultCurrency->symbol : '' }})</span> @endif</div></div>
                </div></div></div>
                <div class="col-md-4"><div class="card border h-100"><div class="card-header bg-light py-2"><h6 class="mb-0 fw-bold"><i class="bx bx-pulse me-2 text-primary"></i>{{ __('Statut') }}</h6></div><div class="card-body text-center"><span class="badge {{ $variant->status->badgeClass() }} fs-14 px-3 py-2">{{ $variant->status->label() }}</span><hr>@if ($variant->is_default)<span class="badge badge-soft-success"><i class="bx bxs-star me-1"></i>{{ __('Variante par défaut') }}</span>@else<span class="badge badge-soft-secondary">{{ __('Variante secondaire') }}</span>@endif</div></div></div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab_attributes"><div class="card border"><div class="card-header bg-light py-2"><h6 class="mb-0 fw-bold"><i class="bx bx-layer me-2 text-primary"></i>{{ __("Valeurs d'attributs") }}</h6></div><div class="card-body"><div class="d-flex flex-wrap gap-2">@forelse ($variant->attributeValues as $value)<span class="badge badge-soft-primary fs-14 px-3 py-2">@include('v1.backend.pages.products.variants.partials._color-swatch', ['hex' => $value->hex_color]) {{ $value->attribute?->active_translation?->name ?? '?' }} : {{ $value->active_translation?->label ?? $value->value }}</span>@empty<span class="text-muted">{{ __("Aucune valeur d'attribut rattachée.") }}</span>@endforelse</div></div></div></div>

        <div class="tab-pane fade" id="tab_images"><div class="card border"><div class="card-header bg-light py-2"><h6 class="mb-0 fw-bold"><i class="bx bx-image me-2 text-primary"></i>{{ __('Galerie de la variante') }}</h6></div><div class="card-body">
            @if ($variantImages->isNotEmpty())
                <div class="d-flex flex-wrap gap-3">@foreach ($variantImages as $image)<a href="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}" target="_blank" rel="noopener"><img src="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}" alt="{{ __('Image :number', ['number' => $loop->iteration]) }}" class="rounded border" style="width:140px;height:140px;object-fit:cover"></a>@endforeach</div>
            @else
                <p class="text-muted mb-0">{{ __('Aucune image propre à cette variante. La boutique peut alors utiliser les images du produit parent.') }}</p>
            @endif
        </div></div></div>

        <div class="tab-pane fade" id="tab_meta"><div class="row g-4"><div class="col-md-6"><div class="card border h-100"><div class="card-header bg-light py-2"><h6 class="mb-0 fw-bold"><i class="bx bx-time-five me-2 text-primary"></i>{{ __('Métadonnées') }}</h6></div><div class="card-body"><div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div><div class="col-8">@formatDate($variant->created_at, 'D MMMM YYYY - HH:mm:ss')</div></div><hr class="my-2"><div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div><div class="col-8">@formatDate($variant->updated_at, 'D MMMM YYYY - HH:mm:ss')</div></div>@if($variant->deleted_at)<hr class="my-2"><div class="row align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div><div class="col-8">@formatDate($variant->deleted_at, 'D MMMM YYYY - HH:mm:ss')</div></div>@endif</div></div></div></div></div>
    </div>

    @include('v1.backend.pages.products.variants.partials._show-end', ['variant' => $variant])
@endsection

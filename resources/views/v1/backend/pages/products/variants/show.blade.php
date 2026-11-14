@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la variante') }} — <code>{{ $variant->sku }}</code>
@endsection

@section('content')
    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de la variante')])
    @include('v1.backend.pages.products.variants.partials._show-start', ['variant' => $variant])

    @php
        $defaultCurrency = \\App\\Models\\Currency::where('is_default', true)->first();
        $variantImages = $variant->cloudFiles->where('file_type', 'image')->sortBy(fn ($file) => $file->metadata['page'] ?? 0)->values();
    @endphp

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab_general">{{ __('Informations générales') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_attributes">{{ __("Valeurs d'attributs") }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_images">{{ __('Images') }} <span class="badge bg-primary ms-1">{{ $variantImages->count() }}</span></a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_meta">{{ __('Métadonnées') }}</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="tab_general">
            <div class="row g-4">
                <div class="col-md-8"><div class="card border h-100"><div class="card-header bg-light py-2"><h6 class="mb-0 fw-bold">{{ __('Détails') }}</h6></div><div class="card-body">
                    <div class="row mb-3"><div class="col-4 text-muted fw-semibold">{{ __('Produit parent') }}</div><div class="col-8"><a href="{{ route('backend.products.show', ['product' => $variant->product?->uuid, 'slug' => $variant->product?->active_translation?->slug ?? 'null']) }}">{{ $variant->product?->active_translation?->name ?? ('#'.$variant->product_id) }}</a></div></div><hr>
                    <div class="row mb-3"><div class="col-4 text-muted fw-semibold">UUID</div><div class="col-8"><code>{{ $variant->uuid }}</code></div></div><hr>
                    <div class="row mb-3"><div class="col-4 text-muted fw-semibold">SKU</div><div class="col-8"><code>{{ $variant->sku }}</code></div></div><hr>
                    <div class="row mb-3"><div class="col-4 text-muted fw-semibold">{{ __('Code-barres') }}</div><div class="col-8">{{ $variant->barcode ?? '—' }}</div></div><hr>
                    <div class="row"><div class="col-4 text-muted fw-semibold">{{ __('Prix') }}</div><div class="col-8">{{ $variant->effectivePrice() }}{{ $defaultCurrency?->symbol ? ' '.$defaultCurrency->symbol : '' }}</div></div>
                </div></div></div>
                <div class="col-md-4"><div class="card border"><div class="card-body text-center"><span class="badge {{ $variant->status->badgeClass() }}">{{ $variant->status->label() }}</span><hr>@if ($variant->is_default){{ __('Variante par défaut') }}@else{{ __('Variante secondaire') }}@endif</div></div></div>
            </div>
        </div>
        <div class="tab-pane fade" id="tab_attributes"><div class="card border"><div class="card-body"><div class="d-flex flex-wrap gap-2">@forelse ($variant->attributeValues as $value)<span class="badge badge-soft-primary">@include('v1.backend.pages.products.variants.partials._color-swatch', ['hex' => $value->hex_color]) {{ $value->attribute?->active_translation?->name ?? '?' }} : {{ $value->active_translation?->label ?? $value->value }}</span>@empty<span class="text-muted">{{ __("Aucune valeur d'attribut rattachée.") }}</span>@endforelse</div></div></div></div>
        <div class="tab-pane fade" id="tab_images"><div class="card border"><div class="card-header"><h6 class="mb-0">{{ __('Galerie de la variante') }}</h6></div><div class="card-body">
            @if ($variantImages->isNotEmpty())
                <div class="d-flex flex-wrap gap-3">@foreach ($variantImages as $image)<a href="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}" target="_blank" rel="noopener"><img src="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}" alt="{{ __('Image :number', ['number' => $loop->iteration]) }}" class="rounded border" style="width:140px;height:140px;object-fit:cover"></a>@endforeach</div>
            @else
                <p class="text-muted mb-0">{{ __('Aucune image propre à cette variante. Les images du produit parent restent une solution de repli pour le storefront.') }}</p>
            @endif
        </div></div></div>
        <div class="tab-pane fade" id="tab_meta"><div class="card border"><div class="card-body"><div>{{ __('Créé le') }}: @formatDate($variant->created_at, 'D MMMM YYYY - HH:mm:ss')</div><div>{{ __('Modifié le') }}: @formatDate($variant->updated_at, 'D MMMM YYYY - HH:mm:ss')</div></div></div></div>
    </div>

    @include('v1.backend.pages.products.variants.partials._show-end', ['variant' => $variant])
@endsection

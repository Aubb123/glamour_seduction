@if ($product)
    @php($parentImage = $product->getFirstImage())
    @php($parentUrl = route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']))

    <div class="card border mb-4">
        <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="mb-0 fw-bold">
                <i class="bx bx-link me-2 text-primary"></i>{{ __('Produit parent') }}
            </h6>

            <a href="{{ $parentUrl }}" class="btn btn-sm btn-soft-primary">
                <i class="bx bx-show me-1"></i>{{ __('Voir le produit') }}
            </a>
        </div>

        <div class="card-body py-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">

                <a href="{{ $parentUrl }}" class="flex-shrink-0">
                    <img src="{{ $parentImage['url_img'] }}"
                         alt="{{ $product->active_translation?->name ?? __('Image produit') }}"
                         style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:1px solid #dee2e6;">
                </a>

                <div class="flex-grow-1">
                    <a href="{{ $parentUrl }}" class="fw-semibold text-decoration-none d-block">
                        {{ $product->active_translation?->name ?? ('#'.$product->id) }}
                    </a>
                    <small class="text-muted d-block"><code>{{ $product->uuid }}</code></small>
                </div>

                <div class="text-end">
                    @if ($product->status)
                        <span class="badge {{ $product->status->badgeClass() }}">{{ $product->status->label() }}</span>
                    @endif
                    <small class="text-muted d-block mt-1">
                        {{ number_format((float) $product->base_price, 2, ',', ' ') }}{{ $product->currency?->symbol ? ' '.$product->currency->symbol : '' }}
                    </small>
                </div>

            </div>
        </div>
    </div>
@endif

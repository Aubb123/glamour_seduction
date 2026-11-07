@php
    // Repli : si le contrôleur ne fournit pas la collection, on la charge ici.
    // Les stocks relient l'entrepôt à une variante, elle-même rattachée à un produit.
    $stocks = $stocks ?? $warehouse->stocks()->with(['variant.product'])->orderByDesc('quantity')->get();
@endphp

<div class="col-12">
    <div class="card border">
        <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold"><i class="bx bx-package me-2 text-primary"></i>{{ __('Produits en stock') }}</h6>
            <span class="badge badge-soft-primary">{{ $stocks->count() }} {{ __('ligne(s) de stock') }}</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-centered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Produit') }}</th>
                            <th>SKU</th>
                            <th class="text-end">{{ __('Quantité') }}</th>
                            <th class="text-end">{{ __('Réservée') }}</th>
                            <th class="text-end">{{ __('Disponible') }}</th>
                            <th class="text-center">{{ __('Seuil') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stocks as $stock)
                            @php($product = $stock->variant?->product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($product)
                                        <a href="{{ route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']) }}" class="text-decoration-none fw-semibold">
                                            {{ $product->active_translation?->name ?? ('#'.$product->id) }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td><code>{{ $stock->variant?->sku ?? '—' }}</code></td>
                                <td class="text-end fw-bold">{{ $stock->quantity }}</td>
                                <td class="text-end text-muted">{{ $stock->reserved_quantity }}</td>
                                <td class="text-end">
                                    <span class="badge {{ $stock->availableQuantity() > 0 ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                        {{ $stock->availableQuantity() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($stock->low_stock_threshold)
                                        <span class="badge {{ $stock->quantity <= $stock->low_stock_threshold ? 'badge-soft-warning' : 'badge-soft-secondary' }}">{{ $stock->low_stock_threshold }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">{{ __('Aucun produit en stock dans cet entrepôt.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($stocks->isNotEmpty())
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">{{ __('Total') }}</td>
                                <td class="text-end">{{ $stocks->sum('quantity') }}</td>
                                <td class="text-end">{{ $stocks->sum('reserved_quantity') }}</td>
                                <td class="text-end">{{ $stocks->sum(fn ($s) => $s->availableQuantity()) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

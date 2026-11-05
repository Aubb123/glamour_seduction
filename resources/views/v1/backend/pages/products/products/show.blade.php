@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du produit') }} — <code>{{ $product->active_translation?->name ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail du produit')])

        @include('v1.backend.pages.products.products.partials._show-start', ['product' => $product])

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
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_variants">
                        <i class="bx bx-layer me-1"></i> {{ __('Variantes') }}
                        @if($variants->isNotEmpty())
                            <span class="badge bg-primary ms-1">{{ $variants->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_multimedia">
                        <i class="bx bx-image me-1"></i> {{ __('Multimédias') }}
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
                        @php($defaultCurrency = \App\Models\Currency::where('is_default', true)->first())

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
                                        <div class="col-8"><code>{{ $product->uuid }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Catégorie') }}</div>
                                        <div class="col-8 fw-semibold">
                                            @if($product->category)
                                                <a href="{{ route('backend.product-categories.show', ['productCategory' => $product->category->uuid, 'slug' => $product->category?->active_translation?->slug ?? 'null']) }}"
                                                    class="badge badge-soft-primary text-decoration-none">
                                                    {{ $product->category?->active_translation?->name ?? '—' }}
                                                </a>
                                            @else
                                                <span class="badge badge-soft-primary">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Marque') }}</div>
                                        <div class="col-8 fw-semibold">
                                            @if($product->brand)
                                                <a href="{{ route('backend.product-brands.show', ['productBrand' => $product->brand->uuid, 'slug' => $product->brand?->active_translation?->slug ?? 'null']) }}"
                                                    class="badge badge-soft-primary text-decoration-none">
                                                    {{ $product->brand?->active_translation?->name ?? '—' }}
                                                </a>
                                            @else
                                                <span class="badge badge-soft-primary">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Tags') }}</div>
                                        <div class="col-8">
                                            @forelse($product->tags as $tag)
                                                <span class="badge badge-soft-info me-1 text-decoration-none">
                                                    {{ $tag->active_translation?->name ?? '—' }}
                                                </span>
                                            @empty
                                                <span class="text-muted">—</span>
                                            @endforelse
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Prix de base') }}</div>
                                        <div class="col-8 fw-bold">
                                            {{ number_format((float) $product->base_price, 2, ',', ' ') }}{{ $defaultCurrency?->symbol ? ' '.$defaultCurrency->symbol : '' }}
                                            @if($defaultCurrency)
                                                <span class="text-muted fw-normal small ms-1">({{ __('devise par défaut') }} : {{ $defaultCurrency->active_translation?->name ?? $defaultCurrency->code }})</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Poids') }}</div>
                                        <div class="col-8">{{ $product->weight ? $product->weight.' kg' : '—' }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Statut') }}</div>
                                        <div class="col-8">
                                            <span class="badge {{ $product->status->badgeClass() }} fs-14 px-3 py-2">
                                                {{ $product->status->label() }}
                                            </span>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Mis en avant') }}</div>
                                        <div class="col-8">
                                            @if($product->is_featured)
                                                <span class="badge badge-soft-success">{{ __('Oui') }}</span>
                                            @else
                                                <span class="badge badge-soft-secondary">{{ __('Non') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Nombre de vues') }}</div>
                                        <div class="col-8">{{ number_format($product->views_count) }}</div>
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
                                    <span class="badge {{ $product->status->badgeClass() }} fs-14 px-3 py-2">
                                        {{ $product->status->label() }}
                                    </span>
                                    <hr>
                                    @if ($product->is_featured)
                                        <span class="badge badge-soft-success">{{ __('Produit mis en avant') }}</span>
                                    @else
                                        <span class="badge badge-soft-secondary">{{ __('Produit standard') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card border">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bx bx-file me-2 text-primary"></i>{{ __('Description') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    {!! $product->active_translation?->description ?? '' !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Traductions -->
                <div class="tab-pane fade" id="tab_translations">
                    @include('v1.backend.pages.products.products.translations.index', [
                        'product' => $product,
                        'translations' => $product->translations,
                    ])
                </div>

                <!-- Variantes -->
                <div class="tab-pane fade" id="tab_variants">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="card border">
                                <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bx bx-layer me-2 text-primary"></i>{{ __('Variantes') }}
                                    </h6>
                                    <a href="{{ route('backend.product-variants.create', ['product' => $product->uuid]) }}" class="btn btn-sm btn-primary">
                                        <i class="bx bx-plus me-1"></i> {{ __('Nouvelle variante') }}
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle w-100">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('SKU') }}</th>
                                                    <th>{{ __("Valeurs d'attributs") }}</th>
                                                    <th>{{ __('Prix') }}</th>
                                                    <th>{{ __('Statut') }}</th>
                                                    <th>{{ __('Par défaut') }}</th>
                                                    <th class="text-center">{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($variants as $variant)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td><span class="fw-semibold">{{ $variant->sku }}</span></td>
                                                        <td>
                                                            @forelse ($variant->attributeValues as $value)
                                                                <span class="badge badge-soft-primary me-1">
                                                                    {{ $value->active_translation?->label ?? $value->value }}
                                                                </span>
                                                            @empty
                                                                <span class="text-muted">—</span>
                                                            @endforelse
                                                        </td>
                                                        <td>
                                                            @if ($variant->price)
                                                                {{ number_format((float) $variant->price, 2, ',', ' ') }}{{ $defaultCurrency?->symbol ? ' '.$defaultCurrency->symbol : '' }}
                                                            @else
                                                                <span class="text-muted">{{ __('Hérite du produit') }} ({{ number_format((float) $product->base_price, 2, ',', ' ') }}{{ $defaultCurrency?->symbol ? ' '.$defaultCurrency->symbol : '' }})</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $variant->status->badgeClass() }} fs-14 px-3 py-2">
                                                                {{ $variant->status->label() }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if ($variant->is_default)
                                                                <span class="badge badge-soft-success">{{ __('Par défaut') }}</span>
                                                            @else
                                                                <span class="badge badge-soft-secondary">—</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="d-inline-flex gap-1">
                                                                <a href="{{ route('backend.product-variants.show', ['productVariant' => $variant->uuid]) }}" class="btn btn-sm btn-soft-secondary" title="{{ __('Détails') }}">
                                                                    <i class="bx bx-show"></i>
                                                                </a>
                                                                <a href="{{ route('backend.product-variants.edit', ['productVariant' => $variant->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                                    <i class="bx bx-edit"></i>
                                                                </a>
                                                                <form action="{{ route('backend.product-variants.destroy', ['productVariant' => $variant->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $variant->uuid }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer') }}" onclick="confirmAction('delete-form-{{ $variant->uuid }}', '{{ __('Supprimer cette variante ?') }}', '{{ __('Supprimer') }}', 'warning')">
                                                                        <i class="bx bx-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted py-4">
                                                            <i class="bx bx-layer fs-3 d-block mb-2"></i>
                                                            {{ __('Aucune variante trouvée pour ce produit.') }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Multimédias -->
                <div class="tab-pane fade" id="tab_multimedia">
                    @include('v1.backend.pages.products.products.partials._tab-multimedia', ['product' => $product])
                </div>

                <!-- Métadonnées -->
                <div class="tab-pane fade" id="tab_meta">
                    @include('v1.backend.pages.products.products.partials._show-meta', ['product' => $product])
                </div>

            </div>

        @include('v1.backend.pages.products.products.partials._show-end', ['product' => $product])

@endsection

@push('scripts')
    <script>
        // Bootstrap does not activate a tab from the URL fragment by itself.
        // Respect #tab_variants (and other tab fragments) after the tab plugin loads.
        document.addEventListener('DOMContentLoaded', function () {
            function activateTabFromHash() {
                if (!window.location.hash || !window.bootstrap || !window.bootstrap.Tab) {
                    return;
                }

                const tabLink = Array.from(document.querySelectorAll('[data-bs-toggle="tab"]'))
                    .find(function (link) {
                        return link.hash === window.location.hash;
                    });

                if (tabLink) {
                    window.bootstrap.Tab.getOrCreateInstance(tabLink).show();
                }
            }

            activateTabFromHash();
            window.addEventListener('hashchange', activateTabFromHash);
        });
    </script>
@endpush

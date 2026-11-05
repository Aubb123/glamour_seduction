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
                                        <div class="col-4 text-muted fw-semibold">{{ __('Prix de base') }}</div>
                                        <div class="col-8 fw-bold">{{ number_format((float) $product->base_price, 2, ',', ' ') }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Prix barré') }}</div>
                                        <div class="col-8">
                                            @if ($product->compare_at_price)
                                                <del>{{ number_format((float) $product->compare_at_price, 2, ',', ' ') }}</del>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Poids') }}</div>
                                        <div class="col-8">{{ $product->weight ? $product->weight . ' kg' : '—' }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Vues') }}</div>
                                        <div class="col-8">{{ number_format($product->views_count) }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Emballage discret') }}</div>
                                        <div class="col-8">
                                            @if ($product->requires_discreet_packaging)
                                                <span class="badge badge-soft-success">{{ __('Requis') }}</span>
                                            @else
                                                <span class="badge badge-soft-secondary">{{ __('Non requis') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row align-items-start">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Tags') }}</div>
                                        <div class="col-8">
                                            @forelse ($product->tags as $tag)
                                                <a href="{{ route('backend.product-tags.show', ['productTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}"
                                                    class="badge badge-soft-info me-1 text-decoration-none">
                                                    {{ $tag->active_translation?->name ?? '#' . $tag->id }}
                                                </a>
                                            @empty
                                                <span class="text-muted">—</span>
                                            @endforelse
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
                                            style="background: {{ $product->status === \App\Enums\Products\Products\ProductStatus::Published ? '#e8f9f0' : '#fef0f0' }}">
                                        <div>
                                            <div class="fw-semibold">{{ __('Statut') }}</div>
                                            <small class="text-muted">
                                                @if ($product->status === \App\Enums\Products\Products\ProductStatus::Published)
                                                    {{ __('En vente sur la boutique') }}
                                                @elseif ($product->status === \App\Enums\Products\Products\ProductStatus::Draft)
                                                    {{ __('Brouillon — visible uniquement côté admin') }}
                                                @elseif ($product->status === \App\Enums\Products\Products\ProductStatus::Archived)
                                                    {{ __('Retiré de la vente') }}
                                                @else
                                                    {{ __('Statut inconnu') }}
                                                @endif
                                            </small>
                                        </div>
                                        <span class="{{ $product->status?->badgeClass() ?? 'badge badge-soft-secondary' }} px-3 py-2">
                                            {{ $product->status?->label() ?? '—' }}
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: #f8f8f8;">
                                        <div>
                                            <div class="fw-semibold">{{ __('Mise en avant') }}</div>
                                            <small class="text-muted">{{ __('Produit phare sur la boutique') }}</small>
                                        </div>
                                        @if ($product->is_featured)
                                            <span class="badge badge-soft-warning px-3 py-2"><i class="bx bxs-star me-1"></i> {{ __('Oui') }}</span>
                                        @else
                                            <span class="badge badge-soft-secondary px-3 py-2">{{ __('Non') }}</span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Traductions -->
                <div class="tab-pane fade" id="tab_translations">

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
                            @php($translation = getTranslation($product, $lang->id))
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
                                                    <div class="col-2 text-muted fw-semibold">{{ __('Description courte') }}</div>
                                                    <div class="col-9" style="max-height: 200px; overflow-y: auto;">
                                                        {{ $translation?->short_description ?? '—' }}
                                                    </div>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row mb-3 align-items-start">
                                                    <div class="col-2 text-muted fw-semibold">{{ __('Description') }}</div>
                                                    <div class="col-9" style="max-height: 200px; overflow-y: auto;">
                                                        {{ $translation?->description ?? '—' }}
                                                    </div>
                                                </div>
                                                <hr class="my-2">

                                                <div class="row align-items-start">
                                                    <div class="col-2 text-muted fw-semibold">{{ __('Actions') }}</div>
                                                    <div class="col-9">
                                                        @if($translation && !$translation->trashed())
                                                            <form action="{{ route('backend.product-translations.destroy', ['uuid' => $translation->uuid]) }}" method="POST" id="delete-form-{{ $translation->uuid }}" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $translation->uuid }}', '{{ __('Supprimer cette traduction ?') }}', '{{ __('Supprimer') }}', 'warning')">
                                                                    <i class="bx bx-trash me-1"></i> {{ __('Supprimer') }}
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
                                            @if ($productTranslations->isEmpty())
                                                <p class="text-muted mb-0">{{ __('Aucune traduction supprimée.') }}</p>
                                            @else
                                                @include('v1.backend.pages.products.products.partials._trashed-table-translations', [
                                                    'productTranslations' => $productTranslations,
                                                    'product' => $product
                                                ])
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                    <a href="{{ route('backend.product-variants.create') }}" class="btn btn-sm btn-primary">
                                        <i class="bx bx-plus me-1"></i> {{ __('Nouvelle variante') }}
                                    </a>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-centered mb-0">
                                            <thead>
                                                <tr>
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
                                                        <td><code>{{ $variant->sku }}</code></td>
                                                        <td>
                                                            @forelse ($variant->attributeValues as $value)
                                                                <span class="badge badge-soft-primary me-1">{{ $value->active_translation?->label ?? $value->value }}</span>
                                                            @empty
                                                                <span class="text-muted">—</span>
                                                            @endforelse
                                                        </td>
                                                        <td>
                                                            @if ($variant->price)
                                                                <span class="fw-bold">{{ number_format((float) $variant->price, 2, ',', ' ') }}</span>
                                                            @else
                                                                <span class="text-muted">{{ __('Hérite du produit') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $variant->status->badgeClass() }}">{{ $variant->status->label() }}</span>
                                                        </td>
                                                        <td>
                                                            @if ($variant->is_default)
                                                                <span class="badge badge-soft-success">{{ __('Par défaut') }}</span>
                                                            @else
                                                                <span class="badge badge-soft-secondary">—</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="{{ route('backend.product-variants.show', ['productVariant' => $variant->uuid]) }}" class="btn btn-sm btn-soft-secondary" title="{{ __('Détails') }}">
                                                                <i class="bx bx-show"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">{{ __('Aucune variante pour ce produit.') }}</td>
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
                                        <div class="col-8"><code>{{ $product->uuid }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                        <div class="col-8">@formatDate($product->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                        <div class="col-8">@formatDate($product->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                        <div class="col-8">
                                            @if ($product->deleted_at)
                                                @formatDate($product->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

        @include('v1.backend.pages.products.products.partials._show-end', ['product' => $product])
                    
@endsection
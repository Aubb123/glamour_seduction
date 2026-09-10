@extends('v1.backend.layouts.app')

@section('title')
    Détail du produit — <code>{{ $product->active_translation?->name ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Détail du produit"])

    @include('v1.backend.pages.products.products.partials._show-start', ['product' => $product])

        <div class="row g-4">

            <div class="col-md-8">
                <div class="card border h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="bx bx-info-circle me-2 text-primary"></i>Détails
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Nom</div>
                            <div class="col-8">{{ $product->active_translation?->name ?? '-' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Catégorie</div>
                            <div class="col-8">{{ $product->category?->active_translation?->name ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Marque</div>
                            <div class="col-8">{{ $product->brand?->active_translation?->name ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Prix de base</div>
                            <div class="col-8 fw-bold">{{ number_format((float) $product->base_price, 2, ',', ' ') }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Prix barré</div>
                            <div class="col-8">
                                @if ($product->compare_at_price)
                                    <del>{{ number_format((float) $product->compare_at_price, 2, ',', ' ') }}</del>
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Poids</div>
                            <div class="col-8">{{ $product->weight ? $product->weight.' kg' : '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Vues</div>
                            <div class="col-8">{{ $product->views_count }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Emballage discret</div>
                            <div class="col-8">
                                @if ($product->requires_discreet_packaging)
                                    <span class="badge badge-soft-success">Requis</span>
                                @else
                                    <span class="badge badge-soft-secondary">Non requis</span>
                                @endif
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row align-items-center">
                            <div class="col-4 text-muted fw-semibold">Tags</div>
                            <div class="col-8">
                                @forelse ($product->tags as $tag)
                                    <span class="badge badge-soft-primary me-1">{{ $tag->active_translation?->name ?? ('#'.$tag->id) }}</span>
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
                            <i class="bx bx-pulse me-2 text-primary"></i>Statut
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <span class="badge {{ $product->status->badgeClass() }} fs-14 px-3 py-2">
                            {{ $product->status->label() }}
                        </span>
                        <hr>
                        @if ($product->is_featured)
                            <span class="badge badge-soft-warning"><i class="bx bxs-star me-1"></i> Mis en avant</span>
                        @else
                            <span class="badge badge-soft-secondary">Non mis en avant</span>
                        @endif
                        <div class="small text-muted mt-2">
                            <div>UUID : <code>{{ $product->uuid }}</code></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card border">
                    <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">
                            <i class="bx bx-globe me-2 text-primary"></i>Traductions
                        </h6>
                        <a href="{{ route('backend.product-translations.trashed', ['product_uuid' => $product->uuid]) }}" class="btn btn-sm btn-soft-secondary">
                            <i class="bx bx-trash me-1"></i> Traductions supprimées
                            @if ($productTranslations->count() > 0)
                                <span class="badge badge-soft-danger ms-1">{{ $productTranslations->count() }}</span>
                            @endif
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>Langue</th>
                                        <th>Nom</th>
                                        <th>Slug</th>
                                        <th>Description courte</th>
                                        <th>Créé le</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($product->translations as $translation)
                                        <tr>
                                            <td><span class="badge badge-soft-primary">{{ $translation->lang?->code ?? '—' }}</span></td>
                                            <td class="fw-bold">{{ $translation->name ?? '—' }}</td>
                                            <td><code>{{ $translation->slug ?? '—' }}</code></td>
                                            <td class="text-muted" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $translation->short_description ?? '—' }}</td>
                                            <td>@formatDate($translation->created_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Aucune traduction trouvée.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @include('v1.backend.pages.products.products.partials._show-end', ['product' => $product])

@endsection
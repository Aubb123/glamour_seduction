@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la variante') }} — <code>{{ $variant->sku }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de la variante')])

    @include('v1.backend.pages.products.variants.partials._show-start', ['variant' => $variant])

    @php
        $ownImages = $variant->cloudFiles
            ->where('file_type', 'image')
            ->sortBy(fn ($file) => $file->metadata['page'] ?? 0)
            ->values();
        $images = $variant->displayImages();
        $ownImageUuids = $ownImages->pluck('uuid')->all();
        $status = $variant->status;
    @endphp

            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab_variant_general">
                        <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_variant_images">
                        <i class="bx bx-image me-1"></i> {{ __('Images') }}
                        @if ($images->isNotEmpty())
                            <span class="badge bg-primary ms-1">{{ $images->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_variant_meta">
                        <i class="bx bx-time-five me-1"></i> {{ __('Métadonnées') }}
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Détails -->
                <div class="tab-pane fade show active" id="tab_variant_general">
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
                                        <div class="col-8"><code>{{ $variant->uuid }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Référence SKU') }}</div>
                                        <div class="col-8"><code>{{ $variant->sku }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Code-barres') }}</div>
                                        <div class="col-8">
                                            @if ($variant->barcode)
                                                <code>{{ $variant->barcode }}</code>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Produit parent') }}</div>
                                        <div class="col-8">
                                            @if ($variant->product)
                                                <a href="{{ route('backend.products.show', ['product' => $variant->product->uuid, 'slug' => $variant->product->active_translation?->slug ?? 'null']) }}"
                                                   class="badge badge-soft-primary text-decoration-none">
                                                    <i class="bx bx-link-external me-1"></i>{{ $variant->product->active_translation?->name ?? ('#'.$variant->product->id) }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row align-items-start">
                                        <div class="col-4 text-muted fw-semibold">{{ __("Valeurs d'attributs") }}</div>
                                        <div class="col-8">
                                            @forelse ($variant->attributeValues as $value)
                                                <a href="{{ route('backend.product-attribute-values.show', ['productAttributeValue' => $value->uuid, 'slug' => $value->active_translation?->slug ?? 'null']) }}"
                                                   class="badge badge-soft-primary fw-normal me-1 mb-1 text-decoration-none">
                                                    @include('v1.backend.pages.products.variants.partials._color-swatch', ['hex' => $value->hex_color])
                                                    {{ $value->active_translation?->label ?? $value->value }}
                                                </a>
                                            @empty
                                                <span class="text-muted">{{ __("Aucune valeur d'attribut.") }}</span>
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
                                        <i class="bx bx-purchase-tag me-2 text-primary"></i>{{ __('Prix & statut') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-5 text-muted fw-semibold">{{ __('Prix effectif') }}</div>
                                        <div class="col-7">
                                            <span class="fw-semibold">{{ $variant->effectivePrice() }}</span>
                                            @if ($variant->price === null)
                                                <div class="small text-muted">{{ __('hérite du prix du produit') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-5 text-muted fw-semibold">{{ __('Statut') }}</div>
                                        <div class="col-7">
                                            @if ($status)
                                                <span class="{{ $status->badgeClass() }}">{{ $status->label() }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row align-items-center">
                                        <div class="col-5 text-muted fw-semibold">{{ __('Variante par défaut') }}</div>
                                        <div class="col-7">
                                            @if ($variant->is_default)
                                                <span class="badge badge-soft-success">{{ __('Oui') }}</span>
                                            @else
                                                <span class="badge badge-soft-secondary">{{ __('Non') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="tab-pane fade" id="tab_variant_images">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="card border h-100">
                                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bx bx-image me-2 text-primary"></i>{{ __('Images de la variante') }}
                                    </h6>
                                    <span class="badge bg-primary">{{ $ownImages->count() }} / 4</span>
                                </div>
                                <div class="card-body">
                                    @if ($images->isNotEmpty())
                                        <div class="row g-2">
                                            @foreach ($images as $image)
                                                @php($isOwned = in_array($image->uuid, $ownImageUuids, true))
                                                <div class="col-md-3 col-sm-4 col-6">
                                                    <div class="position-relative border rounded overflow-hidden bg-light" style="height:150px;">
                                                        <a href="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}" target="_blank" rel="noopener noreferrer" class="d-block w-100 h-100">
                                                            <img src="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}"
                                                                 alt="{{ __('Image') }} {{ $image->metadata['page'] ?? $loop->iteration }}"
                                                                 class="w-100 h-100" style="object-fit:cover;">
                                                        </a>

                                                        @if ($isOwned)
                                                            <form action="{{ route('backend.product-variants.update', ['productVariant' => $variant->uuid]) }}"
                                                                  method="POST" id="remove-variant-image-{{ $image->uuid }}"
                                                                  class="position-absolute top-0 end-0 m-2">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="remove_cloud_file_uuids[]" value="{{ $image->uuid }}">
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                        onclick="confirmAction('remove-variant-image-{{ $image->uuid }}', @js(__('Retirer cette image de la variante ?')), @js(__('Retirer')), 'warning')"
                                                                        title="{{ __('Retirer cette image') }}">
                                                                    <i class="bx bx-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <div class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-50 text-white text-center small py-1">
                                                            {{ __('Page') }} {{ $image->metadata['page'] ?? $loop->iteration }}
                                                            @unless ($isOwned)
                                                                — {{ __('image du produit parent') }}
                                                            @endunless
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <p class="text-muted small mt-3 mb-0">
                                            {{ __('Les images du produit parent sont affichées en complément et ne peuvent être retirées que depuis la fiche produit.') }}
                                        </p>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="bx bx-image" style="font-size:4rem;color:#dee2e6;"></i>
                                            <p class="text-muted mt-3 mb-0">{{ __("Aucune image n'est définie pour cette variante ou pour son produit.") }}</p>
                                        </div>
                                    @endif
                                    <p class="text-muted small mt-2 mb-0">{{ __('Quatre images maximum par variante.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Métadonnées -->
                <div class="tab-pane fade" id="tab_variant_meta">
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
                                        <div class="col-8"><code>{{ $variant->uuid }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                        <div class="col-8">@formatDate($variant->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                        <div class="col-8">@formatDate($variant->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                        <div class="col-8">
                                            @if ($variant->deleted_at)
                                                @formatDate($variant->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.products.variants.partials._show-end', ['variant' => $variant])

@endsection

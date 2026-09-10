<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    Variante — {{ $variant->sku }}
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.product-variants.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Retour
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog me-1"></i> Actions
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                            <li>
                                <a href="{{ route('backend.product-variants.edit', ['productVariant' => $variant->uuid]) }}" class="dropdown-item">
                                    <i class="bx bx-edit me-2 text-primary"></i> Modifier
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            @if ($variant->status === \App\Enums\Products\Variants\ProductVariantStatus::Active)
                                <li>
                                    <form action="{{ route('backend.product-variants.deactivate', ['productVariant' => $variant->uuid]) }}" method="POST" id="deactivate-form-{{ $variant->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $variant->uuid }}', 'Désactiver cette variante ?', 'Désactiver', 'warning')">
                                            <i class="bx bx-toggle-left me-2 text-warning"></i> Désactiver
                                        </button>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <form action="{{ route('backend.product-variants.activate', ['productVariant' => $variant->uuid]) }}" method="POST" id="activate-form-{{ $variant->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $variant->uuid }}', 'Activer cette variante ?', 'Activer', 'success')">
                                            <i class="bx bx-toggle-right me-2 text-success"></i> Activer
                                        </button>
                                    </form>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('backend.product-variants.destroy', ['productVariant' => $variant->uuid]) }}" method="POST" id="delete-form-{{ $variant->uuid }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $variant->uuid }}', 'Supprimer cette variante ?', 'Supprimer', 'warning')">
                                        <i class="bx bx-trash me-2 text-danger"></i> Supprimer
                                    </button>
                                </form>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>

            <div class="card-body">

                @include('v1.backend.pages.products.variants.partials._show-navigation', ['variant' => $variant])
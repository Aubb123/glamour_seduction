<div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        Produit — {{ $product->active_translation?->name ?? $product->uuid }}
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.products.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> Actions
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                <li>
                                    <a href="{{ route('backend.products.edit', ['product' => $product->uuid]) }}" class="dropdown-item">
                                        <i class="bx bx-edit me-2 text-primary"></i> Modifier
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                @if ($product->status === \App\Enums\Products\Products\ProductStatus::Published)
                                    <li>
                                        <form action="{{ route('backend.products.unpublish', ['product' => $product->uuid]) }}" method="POST" id="unpublish-form-{{ $product->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('unpublish-form-{{ $product->uuid }}', 'Dépublier ce produit ?', 'Dépublier', 'warning')">
                                                <i class="bx bx-toggle-left me-2 text-warning"></i> Dépublier
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.products.publish', ['product' => $product->uuid]) }}" method="POST" id="publish-form-{{ $product->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('publish-form-{{ $product->uuid }}', 'Publier ce produit ?', 'Publier', 'success')">
                                                <i class="bx bx-toggle-right me-2 text-success"></i> Publier
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                @if ($product->status !== \App\Enums\Products\Products\ProductStatus::Archived)
                                    <li>
                                        <form action="{{ route('backend.products.archive', ['product' => $product->uuid]) }}" method="POST" id="archive-form-{{ $product->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('archive-form-{{ $product->uuid }}', 'Archiver ce produit ?', 'Archiver', 'warning')">
                                                <i class="bx bx-archive me-2 text-secondary"></i> Archiver
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <form action="{{ route('backend.products.destroy', ['product' => $product->uuid]) }}" method="POST" id="delete-form-{{ $product->uuid }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $product->uuid }}', 'Supprimer ce produit ?', 'Supprimer', 'warning')">
                                            <i class="bx bx-trash me-2 text-danger"></i> Supprimer
                                        </button>
                                    </form>
                                </li>

                            </ul>
                        </div>

                    </div>
                </div>

                <div class="card-body">

                    @include('v1.backend.pages.products.products.partials._show-navigation', ['product' => $product])
<div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        Catégorie — {{ $category->active_translation?->name ?? $category->uuid }}
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.product-categories.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> Actions
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                <li>
                                    <a href="{{ route('backend.product-categories.edit', ['productCategory' => $category->uuid]) }}" class="dropdown-item">
                                        <i class="bx bx-edit me-2 text-primary"></i> Modifier
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                @if ($category->status === \App\Enums\Products\Categories\ProductCategoryStatus::Active)
                                    <li>
                                        <form action="{{ route('backend.product-categories.deactivate', ['productCategory' => $category->uuid]) }}" method="POST" id="deactivate-form-{{ $category->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $category->uuid }}', 'Désactiver cette catégorie ?', 'Désactiver', 'warning')">
                                                <i class="bx bx-toggle-left me-2 text-warning"></i> Désactiver
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.product-categories.activate', ['productCategory' => $category->uuid]) }}" method="POST" id="activate-form-{{ $category->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $category->uuid }}', 'Activer cette catégorie ?', 'Activer', 'success')">
                                                <i class="bx bx-toggle-right me-2 text-success"></i> Activer
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <form action="{{ route('backend.product-categories.destroy', ['productCategory' => $category->uuid]) }}" method="POST" id="delete-form-{{ $category->uuid }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $category->uuid }}', 'Supprimer cette catégorie ?', 'Supprimer', 'warning')">
                                            <i class="bx bx-trash me-2 text-danger"></i> Supprimer
                                        </button>
                                    </form>
                                </li>

                            </ul>
                        </div>

                    </div>
                </div>

                <div class="card-body">

                    @include('v1.backend.pages.products.categories.partials._show-navigation', ['category' => $category])

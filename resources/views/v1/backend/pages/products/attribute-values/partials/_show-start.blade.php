<div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        Valeur d'attribut — {{ $value->active_translation?->label ?? $value->value }}
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.product-attribute-values.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> Actions
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                <li>
                                    <a href="{{ route('backend.product-attribute-values.edit', ['productAttributeValue' => $value->uuid]) }}" class="dropdown-item">
                                        <i class="bx bx-edit me-2 text-primary"></i> Modifier
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                @if ($value->status === \App\Enums\Products\Attributes\ProductAttributeValueStatus::Active)
                                    <li>
                                        <form action="{{ route('backend.product-attribute-values.deactivate', ['productAttributeValue' => $value->uuid]) }}" method="POST" id="deactivate-form-{{ $value->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $value->uuid }}', 'Désactiver cette valeur ?', 'Désactiver', 'warning')">
                                                <i class="bx bx-toggle-left me-2 text-warning"></i> Désactiver
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.product-attribute-values.activate', ['productAttributeValue' => $value->uuid]) }}" method="POST" id="activate-form-{{ $value->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $value->uuid }}', 'Activer cette valeur ?', 'Activer', 'success')">
                                                <i class="bx bx-toggle-right me-2 text-success"></i> Activer
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <form action="{{ route('backend.product-attribute-values.destroy', ['productAttributeValue' => $value->uuid]) }}" method="POST" id="delete-form-{{ $value->uuid }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $value->uuid }}', 'Supprimer cette valeur ?', 'Supprimer', 'warning')">
                                            <i class="bx bx-trash me-2 text-danger"></i> Supprimer
                                        </button>
                                    </form>
                                </li>

                            </ul>
                        </div>

                    </div>
                </div>

                <div class="card-body">

                    @include('v1.backend.pages.products.attribute-values.partials._show-navigation', ['value' => $value])
<div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('Produit') }} — <code>{{ Str::limit($product->active_translation?->name ?? '', 30) }}</code>
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.products.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                        </a>

                        <a href="{{ route('backend.products.audits', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']) }}" class="btn btn-soft-primary btn-sm">
                            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
                            <span class="badge bg-primary ms-1">{{ $product->audits_count ?? 0 }}</span>
                        </a>

                        <a href="{{ route('backend.products.activity-logs', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']) }}" class="btn btn-soft-info btn-sm">
                            <i class="bx bx-pulse me-1"></i> {{ __("Logs d'activité") }}
                            <span class="badge bg-primary ms-1">{{ $product->activity_logs_count ?? 0 }}</span>
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                <li>
                                    <a href="{{ route('backend.products.edit', ['product' => $product->uuid]) }}" class="dropdown-item">
                                        <i class="bx bx-edit me-2 text-primary"></i> {{ __('Modifier') }}
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                @if ($product->status === \App\Enums\Products\Products\ProductStatus::Published)
                                    <li>
                                        <form action="{{ route('backend.products.unpublish', ['product' => $product->uuid]) }}" method="POST" id="unpublish-form-{{ $product->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('unpublish-form-{{ $product->uuid }}', '{{ __('Dépublier ce produit ?') }}', '{{ __('Dépublier') }}', 'warning')">
                                                <i class="bx bx-toggle-left me-2 text-warning"></i> {{ __('Dépublier') }}
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.products.publish', ['product' => $product->uuid]) }}" method="POST" id="publish-form-{{ $product->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('publish-form-{{ $product->uuid }}', '{{ __('Publier ce produit ?') }}', '{{ __('Publier') }}', 'success')">
                                                <i class="bx bx-toggle-right me-2 text-success"></i> {{ __('Publier') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                @if ($product->status !== \App\Enums\Products\Products\ProductStatus::Archived)
                                    <li>
                                        <form action="{{ route('backend.products.archive', ['product' => $product->uuid]) }}" method="POST" id="archive-form-{{ $product->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('archive-form-{{ $product->uuid }}', '{{ __('Archiver ce produit ?') }}', '{{ __('Archiver') }}', 'warning')">
                                                <i class="bx bx-archive me-2 text-secondary"></i> {{ __('Archiver') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <form action="{{ route('backend.products.destroy', ['product' => $product->uuid]) }}" method="POST" id="delete-form-{{ $product->uuid }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $product->uuid }}', '{{ __('Supprimer ce produit ?') }}', '{{ __('Supprimer') }}', 'warning')">
                                            <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                        </button>
                                    </form>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <a href="{{ route('backend.product-translations.trashed', ['product_uuid' => $product->uuid]) }}"
                                        class="dropdown-item text-warning">
                                        <i class="bx bx-trash me-2"></i> {{ __('Corbeille des traductions') }}
                                    </a>
                                </li>

                            </ul>
                        </div>

                    </div>
                </div>

                <div class="card-body">

                    @include('v1.backend.pages.products.products.partials._show-navigation', ['product' => $product])
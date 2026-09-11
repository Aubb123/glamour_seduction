<div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('Catégorie') }} — <code>{{ Str::limit($category->active_translation?->name ?? '', 30) }}</code>
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.product-categories.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                        </a>

                        <a href="{{ route('backend.product-categories.audits', ['productCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}" class="btn btn-soft-primary btn-sm">
                            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
                            <span class="badge bg-primary ms-1">{{ $category->audits_count ?? 0 }}</span>
                        </a>

                        <a href="{{ route('backend.product-categories.activity-logs', ['productCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}" class="btn btn-soft-info btn-sm">
                            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
                            <span class="badge bg-primary ms-1">{{ $category->activity_logs_count ?? 0 }}</span>
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                <li>
                                    <a href="{{ route('backend.product-categories.edit', ['productCategory' => $category->uuid]) }}" class="dropdown-item">
                                        <i class="bx bx-edit me-2 text-primary"></i> {{ __('Modifier') }}
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                @if ($category->status === \App\Enums\Products\Categories\ProductCategoryStatus::Active)
                                    <li>
                                        <form action="{{ route('backend.product-categories.deactivate', ['productCategory' => $category->uuid]) }}" method="POST" id="deactivate-form-{{ $category->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $category->uuid }}', '{{ __('Désactiver cette catégorie ?') }}', 'Désactiver', 'warning')">
                                                <i class="bx bx-toggle-left me-2 text-warning"></i> {{ __('Désactiver') }}
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.product-categories.activate', ['productCategory' => $category->uuid]) }}" method="POST" id="activate-form-{{ $category->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $category->uuid }}', '{{ __('Activer cette catégorie ?') }}', 'Activer', 'success')">
                                                <i class="bx bx-toggle-right me-2 text-success"></i> {{ __('Activer') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <form action="{{ route('backend.product-categories.destroy', ['productCategory' => $category->uuid]) }}" method="POST" id="delete-form-{{ $category->uuid }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $category->uuid }}', '{{ __('Supprimer cette catégorie ?') }}', 'Supprimer', 'warning')">
                                            <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                        </button>
                                    </form>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <a href="{{ route('backend.product-category-translations.trashed', ['category_uuid' => $category->uuid]) }}"
                                        class="dropdown-item text-warning">
                                        <i class="bx bx-trash me-2"></i> {{ __('Corbeille des traductions') }}
                                    </a>
                                </li>

                            </ul>
                        </div>

                    </div>

                </div>

                <div class="card-body">

                    @include('v1.backend.pages.products.categories.partials._show-navigation', ['category' => $category])

<div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('Tag') }} — <code>{{ Str::limit($tag->active_translation?->name ?? '', 30) }}</code>
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.product-tags.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                        </a>

                        <a href="{{ route('backend.product-tags.audits', ['productTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}" class="btn btn-soft-primary btn-sm">
                            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
                            <span class="badge bg-primary ms-1">{{ $tag->audits_count ?? 0 }}</span>
                        </a>

                        <a href="{{ route('backend.product-tags.activity-logs', ['productTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}" class="btn btn-soft-info btn-sm">
                            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
                            <span class="badge bg-primary ms-1">{{ $tag->activity_logs_count ?? 0 }}</span>
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                <li>
                                    <a href="{{ route('backend.product-tags.edit', ['productTag' => $tag->uuid]) }}" class="dropdown-item">
                                        <i class="bx bx-edit me-2 text-primary"></i> {{ __('Modifier') }}
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                @if ($tag->status === \App\Enums\Products\Tags\ProductTagStatus::Active)
                                    <li>
                                        <form action="{{ route('backend.product-tags.deactivate', ['productTag' => $tag->uuid]) }}" method="POST" id="deactivate-form-{{ $tag->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $tag->uuid }}', '{{ __('Désactiver cette tag ?') }}', '{{ __('Désactiver') }}', 'warning')">
                                                <i class="bx bx-toggle-left me-2 text-warning"></i> {{ __('Désactiver') }}
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.product-tags.activate', ['productTag' => $tag->uuid]) }}" method="POST" id="activate-form-{{ $tag->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $tag->uuid }}', '{{ __('Activer cette tag ?') }}', '{{ __('Activer') }}', 'success')">
                                                <i class="bx bx-toggle-right me-2 text-success"></i> {{ __('Activer') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <form action="{{ route('backend.product-tags.destroy', ['productTag' => $tag->uuid]) }}" method="POST" id="delete-form-{{ $tag->uuid }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $tag->uuid }}', '{{ __('Supprimer cette tag ?') }}', '{{ __('Supprimer') }}', 'warning')">
                                            <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                        </button>
                                    </form>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <a href="{{ route('backend.product-tag-translations.trashed', ['tag_uuid' => $tag->uuid]) }}"
                                        class="dropdown-item text-warning">
                                        <i class="bx bx-trash me-2"></i> {{ __('Corbeille des traductions') }}
                                    </a>
                                </li>

                            </ul>
                        </div>

                    </div>

                </div>

                <div class="card-body">

                    @include('v1.backend.pages.products.tags.partials._show-navigation', ['tag' => $tag])
<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    {{ __('Galerie') }} — <code>{{ $gallery->active_translation?->title ?? '' }}</code>
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.galleries.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                            <li>
                                <a href="{{ route('backend.galleries.edit', ['gallery' => $gallery->uuid]) }}" class="dropdown-item">
                                    <i class="bx bx-edit me-2 text-primary"></i> {{ __('Modifier') }}
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            @if ($gallery->status === \App\Enums\Galleries\GalleryStatus::Active)
                                <li>
                                    <form action="{{ route('backend.galleries.toggle_active', ['gallery' => $gallery->uuid]) }}" method="POST" id="deactivate-form-{{ $gallery->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $gallery->uuid }}', '{{ __('Désactiver cette galerie ?') }}', 'Désactiver', 'warning')">
                                            <i class="bx bx-toggle-left me-2 text-warning"></i> {{ __('Désactiver') }}
                                        </button>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <form action="{{ route('backend.galleries.toggle_active', ['gallery' => $gallery->uuid]) }}" method="POST" id="activate-form-{{ $gallery->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $gallery->uuid }}', '{{ __('Activer cette galerie ?') }}', 'Activer', 'success')">
                                            <i class="bx bx-toggle-right me-2 text-success"></i> {{ __('Activer') }}
                                        </button>
                                    </form>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('backend.galleries.destroy', ['gallery' => $gallery->uuid]) }}" method="POST" id="delete-form-{{ $gallery->uuid }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $gallery->uuid }}', '{{ __('Supprimer cette galerie ?') }}', 'Supprimer', 'warning')">
                                        <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                    </button>
                                </form>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a href="{{ route('backend.gallery-translations.trashed', ['gallery_uuid' => $gallery->uuid]) }}"
                                    class="dropdown-item text-warning">
                                    <i class="bx bx-trash me-2"></i> {{ __('Corbeille des traductions') }}
                                </a>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>

            <div class="card-body">

                @include('v1.backend.pages.galleries.partials._show-navigation', ['gallery' => $gallery])
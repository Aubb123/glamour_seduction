<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    {{ __('Version') }} — <span class="text-muted">{{ $version->code }}</span>
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.versions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                            <li>
                                <a href="{{ route('backend.versions.edit', ['platformVersion' => $version->uuid]) }}" class="dropdown-item">
                                    <i class="bx bx-edit me-2 text-primary"></i> {{ __('Modifier') }}
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            @if ($version->is_published)
                                <li>
                                    <form action="{{ route('backend.versions.unpublish', ['platformVersion' => $version->uuid]) }}" method="POST" id="unpublish-form-{{ $version->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('unpublish-form-{{ $version->uuid }}', '{{ __('Dépublier cette version ?') }}', 'Dépublier', 'warning')">
                                            <i class="bx bx-hide me-2 text-warning"></i> {{ __('Dépublier') }}
                                        </button>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <form action="{{ route('backend.versions.publish', ['platformVersion' => $version->uuid]) }}" method="POST" id="publish-form-{{ $version->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('publish-form-{{ $version->uuid }}', '{{ __('Publier cette version ?') }}', 'Publier', 'success')">
                                            <i class="bx bx-globe me-2 text-success"></i> {{ __('Publier') }}
                                        </button>
                                    </form>
                                </li>
                            @endif

                            @if ($version->status === \App\Enums\PlatformVersions\PlatformVersionStatus::Active)
                                <li>
                                    <form action="{{ route('backend.versions.deactivate', ['platformVersion' => $version->uuid]) }}" method="POST" id="deactivate-form-{{ $version->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $version->uuid }}', '{{ __('Désactiver cette version ?') }}', 'Désactiver', 'warning')">
                                            <i class="bx bx-pause me-2 text-warning"></i> {{ __('Désactiver') }}
                                        </button>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <form action="{{ route('backend.versions.activate', ['platformVersion' => $version->uuid]) }}" method="POST" id="activate-form-{{ $version->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $version->uuid }}', '{{ __('Activer cette version ?') }}', 'Activer', 'success')">
                                            <i class="bx bx-play me-2 text-success"></i> {{ __('Activer') }}
                                        </button>
                                    </form>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('backend.versions.destroy', ['platformVersion' => $version->uuid]) }}" method="POST" id="delete-form-{{ $version->uuid }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item text-danger" onclick="confirmDelete('{{ $version->uuid }}', 'Êtes-vous sûr de vouloir supprimer cette version ?')">
                                        <i class="bx bx-trash me-2"></i> {{ __('Supprimer') }}
                                    </button>
                                </form>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a href="{{ route('backend.platform-version-translations.trashed', ['platformVersion' => $version->uuid]) }}" class="dropdown-item text-warning">
                                    <i class="bx bx-trash me-2"></i> {{ __('Corbeille des traductions') }}
                                </a>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>

            <div class="card-body">

                @include('v1.backend.pages.platform-versions.partials._show-navigation', ['version' => $version])
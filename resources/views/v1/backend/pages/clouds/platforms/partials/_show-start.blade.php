<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    {{ __('Plateforme') }} — <code>{{ $platform->name }}</code>
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.cloud-platforms.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                            <li>
                                <a href="{{ route('backend.cloud-platforms.edit', ['cloudPlatform' => $platform->uuid]) }}" class="dropdown-item">
                                    <i class="bx bx-edit me-2 text-primary"></i> {{ __('Modifier') }}
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            @if ($platform->status === \App\Enums\Clouds\Platforms\CloudPlatformStatus::Active)
                                <li>
                                    <form action="{{ route('backend.cloud-platforms.toggle_active', ['cloudPlatform' => $platform->uuid]) }}" method="POST" id="deactivate-form-{{ $platform->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $platform->uuid }}', '{{ __('Désactiver cette plateforme ?') }}', 'Désactiver', 'warning')">
                                            <i class="bx bx-toggle-left me-2 text-warning"></i> {{ __('Désactiver') }}
                                        </button>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <form action="{{ route('backend.cloud-platforms.toggle_active', ['cloudPlatform' => $platform->uuid]) }}" method="POST" id="activate-form-{{ $platform->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $platform->uuid }}', '{{ __('Activer cette plateforme ?') }}', 'Activer', 'success')">
                                            <i class="bx bx-toggle-right me-2 text-success"></i> {{ __('Activer') }}
                                        </button>
                                    </form>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('backend.cloud-platforms.destroy', ['cloudPlatform' => $platform->uuid]) }}" method="POST" id="delete-form-{{ $platform->uuid }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $platform->uuid }}', '{{ __('Supprimer cette plateforme ?') }}', 'Supprimer', 'warning')">
                                        <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                    </button>
                                </form>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>

            <div class="card-body">

                @include('v1.backend.pages.clouds.platforms.partials._show-navigation', ['platform' => $platform])
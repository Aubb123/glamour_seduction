<div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('v1/backend/views/pages/geos/divisions/partials/_show-start.division') }} — <span class="text-muted">{{ $division->active_translation?->name ?? $division->uuid }}</span>
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.geodivisions.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/partials/_show-start.retour') }}
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/partials/_show-start.actions') }}
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                <li>
                                    <a href="{{ route('backend.geodivisions.edit', ['geoDivision' => $division->uuid]) }}" class="dropdown-item">
                                        <i class="bx bx-edit me-2 text-primary"></i> {{ __('v1/backend/views/pages/geos/divisions/partials/_show-start.modifier') }}
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                @if ($division->status === \App\Enums\Geos\Divisions\GeoDivisionStatus::Active)
                                    <li>
                                        <form action="{{ route('backend.geodivisions.deactivate', ['geoDivision' => $division->uuid]) }}" method="POST" id="deactivate-form-{{ $division->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $division->uuid }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_show-start.desactiverCetteDivision') }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_show-start.desactiver') }}', 'warning')">
                                                <i class="bx bx-pause me-2 text-warning"></i> {{ __('v1/backend/views/pages/geos/divisions/partials/_show-start.desactiver') }}
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.geodivisions.activate', ['geoDivision' => $division->uuid]) }}" method="POST" id="activate-form-{{ $division->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $division->uuid }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_show-start.activerCetteDivision') }}', '{{ __('v1/backend/views/pages/geos/divisions/partials/_show-start.activer') }}', 'success')">
                                                <i class="bx bx-play me-2 text-success"></i> {{ __('v1/backend/views/pages/geos/divisions/partials/_show-start.activer') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <a href="{{ route('backend.geodivisions.translation.trashed', ['geoDivision' => $division->uuid]) }}" class="dropdown-item text-warning">
                                        <i class="bx bx-trash me-2"></i> {{ __('v1/backend/views/pages/geos/divisions/partials/_show-start.corbeilleDesTraductions') }}
                                    </a>
                                </li>

                            </ul>
                        </div>

                    </div>
                </div>

                <div class="card-body">

                    @include('v1.backend.pages.geos.divisions.partials._show-navigation', ['division' => $division])
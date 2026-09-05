<div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('v1/backend/views/pages/geos/countries/partials/_show-start.pays') }} — <span class="text-muted">{{ $country->active_translation?->name ?? $country->code }}</span>
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.geocountries.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/geos/countries/partials/_show-start.retour') }}
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> {{ __('v1/backend/views/pages/geos/countries/partials/_show-start.actions') }}
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                <li>
                                    <a href="{{ route('backend.geocountries.edit', ['geoCountry' => $country->uuid]) }}" class="dropdown-item">
                                        <i class="bx bx-edit me-2 text-primary"></i> {{ __('v1/backend/views/pages/geos/countries/partials/_show-start.modifier') }}
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                @if ($country->status === \App\Enums\Geos\Countries\GeoCountryStatus::Active)
                                    <li>
                                        <form action="{{ route('backend.geocountries.deactivate', ['geoCountry' => $country->uuid]) }}" method="POST" id="deactivate-form-{{ $country->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $country->uuid }}', '{{ __('v1/backend/views/pages/geos/countries/partials/_show-start.desactiverCePays') }}', '{{ __('v1/backend/views/pages/geos/countries/partials/_show-start.desactiver') }}', 'warning')">
                                                <i class="bx bx-pause me-2 text-warning"></i> {{ __('v1/backend/views/pages/geos/countries/partials/_show-start.desactiver') }}
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.geocountries.activate', ['geoCountry' => $country->uuid]) }}" method="POST" id="activate-form-{{ $country->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $country->uuid }}', '{{ __('v1/backend/views/pages/geos/countries/partials/_show-start.activerCePays') }}', '{{ __('v1/backend/views/pages/geos/countries/partials/_show-start.activer') }}', 'success')">
                                                <i class="bx bx-play me-2 text-success"></i> {{ __('v1/backend/views/pages/geos/countries/partials/_show-start.activer') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <a href="{{ route('backend.geocountries.translation.trashed', ['geoCountry' => $country->uuid]) }}" class="dropdown-item text-warning">
                                        <i class="bx bx-trash me-2"></i> {{ __('v1/backend/views/pages/geos/countries/partials/_show-start.corbeilleDesTraductions') }}
                                    </a>
                                </li>

                            </ul>
                        </div>

                    </div>
                </div>

                <div class="card-body">

                    @include('v1.backend.pages.geos.countries.partials._show-navigation', ['country' => $country])
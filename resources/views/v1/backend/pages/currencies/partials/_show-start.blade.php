<div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('v1/backend/views/pages/currencies/partials/_show-start.devise') }} — <span class="text-muted">{{ $currency->active_translation?->name ?? $currency->code }}</span>
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.currencies.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/currencies/partials/_show-start.retour') }}
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> {{ __('v1/backend/views/pages/currencies/partials/_show-start.actions') }}
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                <li>
                                    <a href="{{ route('backend.currencies.edit', ['currency' => $currency->uuid]) }}" class="dropdown-item">
                                        <i class="bx bx-edit me-2 text-primary"></i> {{ __('v1/backend/views/pages/currencies/partials/_show-start.modifier') }}
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                @if ($currency->status === \App\Enums\Currencies\CurrencyStatus::Active)
                                    <li>
                                        <form action="{{ route('backend.currencies.deactivate', ['currency' => $currency->uuid]) }}" method="POST" id="deactivate-form-{{ $currency->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $currency->uuid }}', '{{ __('v1/backend/views/pages/currencies/partials/_show-start.desactiverCetteDevise') }}', '{{ __('v1/backend/views/pages/currencies/partials/_show-start.desactiver') }}', 'warning')">
                                                <i class="bx bx-pause me-2 text-warning"></i> {{ __('v1/backend/views/pages/currencies/partials/_show-start.desactiver') }}
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.currencies.activate', ['currency' => $currency->uuid]) }}" method="POST" id="activate-form-{{ $currency->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $currency->uuid }}', '{{ __('v1/backend/views/pages/currencies/partials/_show-start.activerCetteDevise') }}', '{{ __('v1/backend/views/pages/currencies/partials/_show-start.activer') }}', 'success')">
                                                <i class="bx bx-play me-2 text-success"></i> {{ __('v1/backend/views/pages/currencies/partials/_show-start.activer') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>

                                @if (! $currency->trashed() && ! $currency->is_default)
                                    <li>
                                        <form action="{{ route('backend.currencies.destroy', ['currency' => $currency->uuid]) }}" method="POST" id="destroy-form-{{ $currency->uuid }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('destroy-form-{{ $currency->uuid }}', '{{ __('v1/backend/views/pages/currencies/partials/_show-start.supprimerCetteDevise') }}', '{{ __('v1/backend/views/pages/currencies/partials/_show-start.supprimer') }}', 'warning')">
                                                <i class="bx bx-trash me-2 text-danger"></i> {{ __('v1/backend/views/pages/currencies/partials/_show-start.supprimer') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li>
                                    <a href="{{ route('backend.currencies.translation.trashed', ['currency' => $currency->uuid]) }}" class="dropdown-item text-warning">
                                        <i class="bx bx-trash me-2"></i> {{ __('v1/backend/views/pages/currencies/partials/_show-start.corbeilleDesTraductions') }}
                                    </a>
                                </li>

                            </ul>
                        </div>

                    </div>
                </div>

                <div class="card-body">

                    @include('v1.backend.pages.currencies.partials._show-navigation', ['currency' => $currency])

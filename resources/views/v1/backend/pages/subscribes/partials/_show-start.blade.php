<div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">{{ __('Abonné') }} — <code>{{ $subscribe->email }}</code></h5>
                    <div class="hstack gap-2 flex-wrap">
                        <a href="{{ route('backend.subscribes.index') }}" class="btn btn-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}</a>
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-cog me-1"></i> {{ __('Actions') }}</button>
                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">
                                @if ($subscribe->status === \App\Enums\Subscribes\SubscribeStatus::Active)
                                    <li>
                                        <form action="{{ route('backend.subscribes.deactivate', ['subscribe' => $subscribe->uuid]) }}" method="POST" id="deactivate-form-{{ $subscribe->uuid }}">
                                            @csrf @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $subscribe->uuid }}', '{{ __('Désactiver cet abonné ?') }}', 'Désactiver', 'warning')"><i class="bx bx-toggle-left me-2 text-warning"></i> {{ __('Désactiver') }}</button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.subscribes.activate', ['subscribe' => $subscribe->uuid]) }}" method="POST" id="activate-form-{{ $subscribe->uuid }}">
                                            @csrf @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $subscribe->uuid }}', '{{ __('Activer cet abonné ?') }}', 'Activer', 'success')"><i class="bx bx-toggle-right me-2 text-success"></i> {{ __('Activer') }}</button>
                                        </form>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('backend.subscribes.destroy', ['subscribe' => $subscribe->uuid]) }}" method="POST" id="delete-form-{{ $subscribe->uuid }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $subscribe->uuid }}', '{{ __('Supprimer cet abonné ?') }}', 'Supprimer', 'warning')"><i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    @include('v1.backend.pages.subscribes.partials._show-navigation', ['subscribe' => $subscribe])
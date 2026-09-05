<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    {{ __('Rôle') }} — <span class="text-muted">{{ $role->active_translation?->label ?? '—' }}</span>
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.roles.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                            <li>
                                <a href="{{ route('backend.roles.edit', ['role' => $role->uuid]) }}" class="dropdown-item">
                                    <i class="bx bx-edit me-2 text-primary"></i> {{ __('Modifier') }}
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            @if ($role->status === \App\Enums\Roles\RoleStatus::Active)
                                <li>
                                    <form action="{{ route('backend.roles.deactivate', $role->uuid) }}" method="POST" id="deactivate-form-{{ $role->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $role->uuid }}', '{{ __('Désactiver ce rôle ?') }}', 'Désactiver', 'warning')">
                                            <i class="bx bx-pause me-2 text-warning"></i> {{ __('Désactiver') }}
                                        </button>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <form action="{{ route('backend.roles.activate', $role->uuid) }}" method="POST" id="activate-form-{{ $role->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $role->uuid }}', '{{ __('Activer ce rôle ?') }}', 'Activer', 'success')">
                                            <i class="bx bx-play me-2 text-success"></i> {{ __('Activer') }}
                                        </button>
                                    </form>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a href="{{ route('backend.roles.translations.trashed', ['role' => $role->uuid]) }}"
                                    class="dropdown-item text-warning">
                                    <i class="bx bx-trash me-2"></i> {{ __('Corbeille des traductions') }}
                                </a>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>

            <div class="card-body">

                @include('v1.backend.pages.roles.partials._show-navigation', ['role' => $role])



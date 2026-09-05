

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('Utilisateur') }} — {{ $user->first_name }} {{ $user->last_name }}
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                                <li>
                                    <a href="{{ route('backend.users.edit', ['user' => $user->uuid]) }}" class="dropdown-item">
                                        <i class="bx bx-edit me-2 text-primary"></i> {{ __('Modifier') }}
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                @if ($user->status === \App\Enums\Users\UserStatus::Active)
                                    <li>
                                        <form action="{{ route('backend.users.suspend', ['user' => $user->uuid]) }}" method="POST" id="suspend-form-{{ $user->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('suspend-form-{{ $user->uuid }}', '{{ __('Suspendre cet utilisateur ?') }}', 'Suspendre', 'warning')">
                                                <i class="bx bx-pause me-2 text-warning"></i> {{ __('Suspendre') }}
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('backend.users.ban', ['user' => $user->uuid]) }}" method="POST" id="ban-form-{{ $user->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('ban-form-{{ $user->uuid }}', '{{ __('Bannir cet utilisateur ?') }}', 'Bannir', 'danger')">
                                                <i class="bx bx-minus me-2 text-danger"></i> {{ __('Bannir') }}
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('backend.users.activate', ['user' => $user->uuid]) }}" method="POST" id="activate-form-{{ $user->uuid }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $user->uuid }}', '{{ __('Activer cet utilisateur ?') }}', 'Activer', 'success')">
                                                <i class="bx bx-toggle-right me-2 text-success"></i> {{ __('Activer') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif

                            </ul>
                        </div>

                    </div>
                </div>

                <div class="card-body">


                    @include('v1.backend.pages.users.partials._show-navigation', ['user' => $user])
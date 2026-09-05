<!-- ========== Topbar Start ========== -->
<header class="topbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="d-flex align-items-center">
                <div class="topbar-item">
                    <button type="button" class="button-toggle-menu me-2">
                        <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>
                <div class="topbar-item d-none d-md-flex">
                    <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">{{ config('app.name') }}</h4>
                </div>
            </div>

            <div class="d-flex align-items-center gap-1">

                {{-- Création rapide (getCreateLinks) --}}
                <div class="dropdown topbar-item">
                    <button type="button" class="topbar-button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{ __('Ajouter') }}">
                        <iconify-icon icon="solar:add-circle-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-1">
                        @foreach (getCreateLinks() as $link)
                            @if (Route::has($link['route']) && auth()->user()->can($link['permission']))
                                <a class="dropdown-item" href="{{ route($link['route']) }}">
                                    <i class="{{ $link['icon'] }} me-2 align-middle"></i>
                                    <span class="align-middle">{{ $link['label'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Sélecteur de langue (getActiveLangs) --}}
                <div class="dropdown topbar-item">
                    <button type="button" class="topbar-button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @php($currentLang = getActiveLangs()['currentLang'] ?? null)
                        @if ($currentLang && !empty($currentLang->file_path))
                            <img src="{{ asset(getEnvFolder().$currentLang->file_path) }}" alt="{{ $currentLang->name }}" class="rounded-circle" height="24" width="24">
                        @else
                            <iconify-icon icon="solar:global-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end dropdown-lg p-1">
                        @foreach (getActiveLangs()['langs'] as $lang)
                            <a class="dropdown-item {{ (getActiveLangs()['currentLang']->code ?? null) === $lang->code ? 'active' : '' }}"
                               href="{{ route('backend.language.switch', ['lang' => $lang->uuid]) }}">
                                @if (!empty($lang->file_path))
                                    <img src="{{ asset(getEnvFolder().$lang->file_path) }}" alt="{{ $lang->name }}" height="18" width="18" class="me-2 rounded-circle">
                                @endif
                                {{ $lang->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Thème clair/sombre --}}
                <div class="topbar-item">
                    <button type="button" class="topbar-button" id="light-dark-mode">
                        <iconify-icon icon="solar:moon-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>
            {{-- Utilisateur --}}
                <div class="dropdown topbar-item">
                    <a class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:void(0);">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle" width="32" src="{{ asset(getEnvFolder().'backend/assets/images/users/avatar-1.jpg') }}" alt="avatar">
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">{{ __('Bienvenue') }} {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}!</h6>

                        @if (auth()->user()->slug)
                            <a class="dropdown-item" href="{{ route('backend.users.show', ['user' => auth()->user()->uuid, 'slug' => auth()->user()->slug]) }}">
                                <i class="bx bx-user-circle text-muted fs-18 align-middle me-1"></i>
                                <span class="align-middle">{{ __('Mon profil') }}</span>
                            </a>
                        @endif

                        @if (Route::has('backend.settings.index') && auth()->user()->can('settings.index'))
                            <a class="dropdown-item" href="{{ route('backend.settings.index') }}">
                                <i class="bx bx-wallet text-muted fs-18 align-middle me-1"></i>
                                <span class="align-middle">{{ __('Paramètres') }}</span>
                            </a>
                        @endif

                        <div class="dropdown-divider my-1"></div>

                        <form action="{{ route('logout') }}" method="POST" id="logout-form">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger w-100 border-0 bg-transparent text-start">
                                <i class="bx bx-log-out fs-18 align-middle me-1"></i>
                                <span class="align-middle">{{ __('Déconnexion') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- ========== Topbar End ========== -->
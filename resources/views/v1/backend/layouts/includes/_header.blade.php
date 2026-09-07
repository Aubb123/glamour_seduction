<!-- ========== Topbar Start ========== -->
<header class="topbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="d-flex align-items-center">
                <div class="topbar-item">
                    <button type="button" class="button-toggle-menu me-2">
                        <i class="bx bx-menu fs-24 align-middle"></i>
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
                        <i class="bx bx-plus-circle fs-24 align-middle"></i>
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
                            <i class="bx bx-globe fs-24 align-middle"></i>
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
                        <i class="bx bx-moon fs-24 align-middle"></i>
                    </button>
                </div>

                <!-- Theme Setting -->
                <div class="topbar-item d-none d-md-flex">
                    <button type="button" class="topbar-button" id="theme-settings-btn" data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas">
                        <i class="bx bx-cog fs-24 align-middle"></i>
                    </button>
                </div>

                {{-- Utilisateur --}}
                <div class="dropdown topbar-item">
                    <a class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:void(0);">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle" src="{{ auth()->user()->getFirstImage()['url_img'] }}" alt="avatar" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
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

<!-- Right Sidebar (Theme Settings) -->
<div>
    <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="theme-settings-offcanvas">
        <div class="d-flex align-items-center bg-primary p-3 offcanvas-header">
                <h5 class="text-white m-0">Theme Settings</h5>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-0">
                <div data-simplebar class="h-100">
                    <div class="p-3 settings-bar">

                        <div>
                            <h5 class="mb-3 font-16 fw-semibold">Color Scheme</h5>

                            <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-color-light" value="light">
                                    <label class="form-check-label" for="layout-color-light">Light</label>
                            </div>

                            <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-color-dark" value="dark">
                                    <label class="form-check-label" for="layout-color-dark">Dark</label>
                            </div>
                        </div>

                        <div>
                            <h5 class="my-3 font-16 fw-semibold">Topbar Color</h5>

                            <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-topbar-color" id="topbar-color-light" value="light">
                                    <label class="form-check-label" for="topbar-color-light">Light</label>
                            </div>
                            <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-topbar-color" id="topbar-color-dark" value="dark">
                                    <label class="form-check-label" for="topbar-color-dark">Dark</label>
                            </div>
                        </div>


                        <div>
                            <h5 class="my-3 font-16 fw-semibold">Menu Color</h5>

                            <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-color" id="leftbar-color-light" value="light">
                                    <label class="form-check-label" for="leftbar-color-light">
                                        Light
                                    </label>
                            </div>

                            <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-color" id="leftbar-color-dark" value="dark">
                                    <label class="form-check-label" for="leftbar-color-dark">
                                        Dark
                                    </label>
                            </div>
                        </div>

                        <div>
                            <h5 class="my-3 font-16 fw-semibold">Sidebar Size</h5>

                            <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-size-default" value="default">
                                    <label class="form-check-label" for="leftbar-size-default">
                                        Default
                                    </label>
                            </div>

                            <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-size-small" value="condensed">
                                    <label class="form-check-label" for="leftbar-size-small">
                                        Condensed
                                    </label>
                            </div>

                            <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-hidden" value="hidden">
                                    <label class="form-check-label" for="leftbar-hidden">
                                        Hidden
                                    </label>
                            </div>

                            <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-size-small-hover-active" value="sm-hover-active">
                                    <label class="form-check-label" for="leftbar-size-small-hover-active">
                                        Small Hover Active
                                    </label>
                            </div>

                            <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-size-small-hover" value="sm-hover">
                                    <label class="form-check-label" for="leftbar-size-small-hover">
                                        Small Hover
                                    </label>
                            </div>
                        </div>

                    </div>
                </div>
        </div>
        <div class="offcanvas-footer border-top p-3 text-center">
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-danger w-100" id="reset-layout">Reset</button>
                    </div>
                </div>
        </div>
    </div>
</div>
<!-- ========== Topbar End ========== -->
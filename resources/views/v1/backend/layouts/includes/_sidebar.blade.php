<!-- ========== App Menu Start ========== -->
<div class="main-nav">

    <!-- Sidebar Logo -->
    <div class="logo-box">
        <a href="{{ route('backend.index') }}" class="logo-dark">
            <img src="{{ asset(getEnvFolder().'backend/assets/images/logo-sm.png') }}" class="logo-sm" alt="logo sm">
            <img src="{{ asset(getEnvFolder().'backend/assets/images/logo-dark.png') }}" class="logo-lg" alt="logo dark">
        </a>
        <a href="{{ route('backend.index') }}" class="logo-light">
            <img src="{{ asset(getEnvFolder().'backend/assets/images/logo-sm.png') }}" class="logo-sm" alt="logo sm">
            <img src="{{ asset(getEnvFolder().'backend/assets/images/logo-light.png') }}" class="logo-lg" alt="logo light">
        </a>
    </div>

    <!-- Menu Toggle Button (sm-hover) -->
    <button type="button" class="button-sm-hover" aria-label="{{ __('Afficher la sidebar') }}">
        <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone" class="button-sm-hover-icon"></iconify-icon>
    </button>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">

            @foreach (getSidebarMenu() as $section)
                <li class="menu-title">{{ $section['header'] }}</li>

                @foreach ($section['items'] as $item)

                    @if (!empty($item['children']))

                        @php
                            $isActive = isChildRouteActive($item['children']);
                            $collapseId = 'sidebar-'.Str::slug($item['label'].'-'.$loop->index);
                        @endphp

                        <li class="nav-item">
                            <a class="nav-link menu-arrow {{ $isActive ? '' : 'collapsed' }}" href="#{{ $collapseId }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                                <span class="nav-icon"><i class="{{ $item['icon'] }} fs-20"></i></span>
                                <span class="nav-text">{{ $item['label'] }}</span>
                            </a>
                            <div class="collapse {{ $isActive ? 'show' : '' }}" id="{{ $collapseId }}">
                                <ul class="nav sub-navbar-nav">
                                    @foreach ($item['children'] as $child)
                                        @php
                                            $childIsActive = isRouteActive($child['patterns'] ?? [$child['route']]);
                                        @endphp
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link {{ $childIsActive ? 'active' : '' }}" href="{{ route($child['route']) }}">
                                                {{ $child['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>

                    @else

                        <li class="nav-item">
                            <a class="nav-link {{ isRouteActive([$item['route']]) ? 'active' : '' }}" href="{{ route($item['route']) }}">
                                <span class="nav-icon"><i class="{{ $item['icon'] }} fs-20"></i></span>
                                <span class="nav-text"> {{ $item['label'] }} </span>
                            </a>
                        </li>

                    @endif

                @endforeach
            @endforeach

        </ul>
    </div>
</div>
<!-- ========== App Menu End ========== -->
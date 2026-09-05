@foreach ($actions as $action)
    <li>
        <a class="dropdown-item @if (request()->routeIs($action['route'])) active text-primary @endif" href="{{ route($action['route']) }}">
            @isset($action['icon'])
                <i class="{{ $action['icon'] }} me-2 align-middle"></i>
            @else
                <i class="bx bx-circle me-2 align-middle"></i>
            @endisset
            <span class="align-middle">{{ $action['label'] }}</span>
        </a>
    </li>
@endforeach
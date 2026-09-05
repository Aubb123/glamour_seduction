<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.subscribes.show')) active @endif" href="{{ route('backend.subscribes.show', ['subscribe' => $subscribe->uuid]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.subscribes.audits')) active @endif" href="{{ route('backend.subscribes.audits', ['subscribe' => $subscribe->uuid]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $subscribe->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.subscribes.activity-logs')) active @endif" href="{{ route('backend.subscribes.activity-logs', ['subscribe' => $subscribe->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $subscribe->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

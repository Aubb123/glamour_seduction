<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.permissions.show')) active @endif" href="{{ route('backend.permissions.show', ['permission' => $permission->uuid, 'slug' => $permission->slug]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.permissions.roles')) active @endif" href="{{ route('backend.permissions.roles', ['permission' => $permission->uuid, 'slug' => $permission->slug]) }}">
            <i class="bx bx-user me-1"></i> {{ __('Rôles') }}
            <span class="badge bg-primary ms-1">{{ $permission->roles_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.permissions.audits')) active @endif" href="{{ route('backend.permissions.audits', ['permission' => $permission->uuid, 'slug' => $permission->slug]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $permission->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.permissions.activity-logs')) active @endif" href="{{ route('backend.permissions.activity-logs', ['permission' => $permission->uuid, 'slug' => $permission->slug]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $permission->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

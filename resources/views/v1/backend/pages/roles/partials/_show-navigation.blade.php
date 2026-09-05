<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.roles.show')) active @endif" href="{{ route('backend.roles.show', ['role' => $role->uuid, 'slug' => $role->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.roles.permissions')) active @endif" href="{{ route('backend.roles.permissions', ['role' => $role->uuid, 'slug' => $role->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-shield-quarter me-1"></i> {{ __('Permissions') }}
            <span class="badge bg-primary ms-1">{{ $role->permissions_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.roles.users')) active @endif" href="{{ route('backend.roles.users', ['role' => $role->uuid, 'slug' => $role->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-user me-1"></i> {{ __('Utilisateurs') }}
            <span class="badge bg-primary ms-1">{{ $role->users_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.roles.audits')) active @endif" href="{{ route('backend.roles.audits', ['role' => $role->uuid, 'slug' => $role->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $role->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.roles.activity-logs')) active @endif" href="{{ route('backend.roles.activity-logs', ['role' => $role->uuid, 'slug' => $role->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $role->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

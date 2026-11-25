<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geodivisions.show')) active @endif" href="{{ route('backend.geodivisions.show', ['geoDivision' => $division->uuid, 'slug' => $division->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/partials/_show-navigation.details') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geodivisions.children')) active @endif" href="{{ route('backend.geodivisions.children', ['geoDivision' => $division->uuid, 'slug' => $division->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-sitemap me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/partials/_show-navigation.sousDivisions') }}
            <span class="badge bg-primary ms-1">{{ $division->children_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geodivisions.audits')) active @endif" href="{{ route('backend.geodivisions.audits', ['geoDivision' => $division->uuid, 'slug' => $division->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/partials/_show-navigation.audits') }}
            <span class="badge bg-primary ms-1">{{ $division->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geodivisions.activity-logs')) active @endif" href="{{ route('backend.geodivisions.activity-logs', ['geoDivision' => $division->uuid, 'slug' => $division->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/partials/_show-navigation.logsActivite') }}
            <span class="badge bg-primary ms-1">{{ $division->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
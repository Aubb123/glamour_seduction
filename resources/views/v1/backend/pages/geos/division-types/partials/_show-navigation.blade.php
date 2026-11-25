<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geodivisiontypes.show')) active @endif" href="{{ route('backend.geodivisiontypes.show', ['geoDivisionType' => $divisionType->uuid, 'slug' => $divisionType->active_translation?->slug ?? $divisionType->code]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/partials/_show-navigation.details') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geodivisiontypes.audits')) active @endif" href="{{ route('backend.geodivisiontypes.audits', ['geoDivisionType' => $divisionType->uuid]) }}">
            <i class="bx bx-file me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/partials/_show-navigation.audits') }}
            <span class="badge bg-primary ms-1">{{ $divisionType->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geodivisiontypes.activity-logs')) active @endif" href="{{ route('backend.geodivisiontypes.activity-logs', ['geoDivisionType' => $divisionType->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/partials/_show-navigation.logsActivite') }}
            <span class="badge bg-primary ms-1">{{ $divisionType->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>


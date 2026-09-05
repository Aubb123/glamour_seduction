<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geocontinents.show')) active @endif" href="{{ route('backend.geocontinents.show', ['geoContinent' => $continent->uuid, 'slug' => $continent->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geocontinents.countries')) active @endif" href="{{ route('backend.geocontinents.countries', ['geoContinent' => $continent->uuid, 'slug' => $continent->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-map me-1"></i> {{ __('Pays') }}
            <span class="badge bg-primary ms-1">{{ $continent->countries_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geocontinents.audits')) active @endif" href="{{ route('backend.geocontinents.audits', ['geoContinent' => $continent->uuid, 'slug' => $continent->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $continent->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geocontinents.activity-logs')) active @endif" href="{{ route('backend.geocontinents.activity-logs', ['geoContinent' => $continent->uuid, 'slug' => $continent->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $continent->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

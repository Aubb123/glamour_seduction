<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geocountries.show')) active @endif" href="{{ route('backend.geocountries.show', ['geoCountry' => $country->uuid, 'slug' => $country->active_translation?->slug ?? $country->code]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('v1/backend/views/pages/geos/countries/partials/_show-navigation.details') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geocountries.users')) active @endif" href="{{ route('backend.geocountries.users', ['geoCountry' => $country->uuid, 'slug' => $country->active_translation?->slug ?? $country->code]) }}">
            <i class="bx bx-user me-1"></i> {{ __('v1/backend/views/pages/geos/countries/partials/_show-navigation.utilisateurs') }}
            <span class="badge bg-primary ms-1">{{ $country->users_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geocountries.audits')) active @endif" href="{{ route('backend.geocountries.audits', ['geoCountry' => $country->uuid, 'slug' => $country->active_translation?->slug ?? $country->code]) }}">
            <i class="bx bx-file me-1"></i> {{ __('v1/backend/views/pages/geos/countries/partials/_show-navigation.audits') }}
            <span class="badge bg-primary ms-1">{{ $country->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.geocountries.activity-logs')) active @endif" href="{{ route('backend.geocountries.activity-logs', ['geoCountry' => $country->uuid, 'slug' => $country->active_translation?->slug ?? $country->code]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('v1/backend/views/pages/geos/countries/partials/_show-navigation.logsActivite') }}
            <span class="badge bg-primary ms-1">{{ $country->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.currencies.show')) active @endif" href="{{ route('backend.currencies.show', ['currency' => $currency->uuid, 'slug' => $currency->active_translation?->slug ?? $currency->code]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('v1/backend/views/pages/currencies/partials/_show-navigation.details') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.currencies.countries')) active @endif" href="{{ route('backend.currencies.countries', ['currency' => $currency->uuid, 'slug' => $currency->active_translation?->slug ?? $currency->code]) }}">
            <i class="bx bx-map me-1"></i> {{ __('v1/backend/views/pages/currencies/partials/_show-navigation.pays') }}
            <span class="badge bg-primary ms-1">{{ $currency->countries_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.currencies.audits')) active @endif" href="{{ route('backend.currencies.audits', ['currency' => $currency->uuid, 'slug' => $currency->active_translation?->slug ?? $currency->code]) }}">
            <i class="bx bx-file me-1"></i> {{ __('v1/backend/views/pages/currencies/partials/_show-navigation.audits') }}
            <span class="badge bg-primary ms-1">{{ $currency->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.currencies.activity-logs')) active @endif" href="{{ route('backend.currencies.activity-logs', ['currency' => $currency->uuid, 'slug' => $currency->active_translation?->slug ?? $currency->code]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('v1/backend/views/pages/currencies/partials/_show-navigation.logsActivite') }}
            <span class="badge bg-primary ms-1">{{ $currency->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

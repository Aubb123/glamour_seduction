<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.versions.show')) active @endif" href="{{ route('backend.versions.show', ['platformVersion' => $version->uuid, 'slug' => $version->active_translation?->slug ?? $version->code]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.versions.audits')) active @endif" href="{{ route('backend.versions.audits', ['platformVersion' => $version->uuid, 'slug' => $version->active_translation?->slug ?? $version->code]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $version->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.versions.activity-logs')) active @endif" href="{{ route('backend.versions.activity-logs', ['platformVersion' => $version->uuid, 'slug' => $version->active_translation?->slug ?? $version->code]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $version->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

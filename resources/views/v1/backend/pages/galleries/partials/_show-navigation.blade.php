<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.galleries.show')) active @endif" href="{{ route('backend.galleries.show', ['gallery' => $gallery->uuid, 'slug' => $gallery->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.galleries.audits')) active @endif" href="{{ route('backend.galleries.audits', ['gallery' => $gallery->uuid, 'slug' => $gallery->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $gallery->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.galleries.activity-logs')) active @endif" href="{{ route('backend.galleries.activity-logs', ['gallery' => $gallery->uuid, 'slug' => $gallery->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $gallery->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
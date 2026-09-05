<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-platforms.show')) active @endif" href="{{ route('backend.cloud-platforms.show', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-platforms.folders')) active @endif" href="{{ route('backend.cloud-platforms.folders', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]) }}">
            <i class="bx bx-folder me-1"> {{ __('Dossiers') }} </i> 
            <span class="badge bg-primary ms-1">{{ $platform->folders_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-platforms.files')) active @endif" href="{{ route('backend.cloud-platforms.files', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Fichiers') }}
            <span class="badge bg-primary ms-1">{{ $platform->files_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-platforms.audits')) active @endif" href="{{ route('backend.cloud-platforms.audits', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $platform->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-platforms.activity-logs')) active @endif" href="{{ route('backend.cloud-platforms.activity-logs', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $platform->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

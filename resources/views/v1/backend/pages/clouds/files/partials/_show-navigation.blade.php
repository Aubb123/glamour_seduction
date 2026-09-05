<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-files.show')) active @endif" href="{{ route('backend.cloud-files.show', ['cloudFile' => $file->uuid]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-files.audits')) active @endif" href="{{ route('backend.cloud-files.audits', ['cloudFile' => $file->uuid]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $file->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-files.activity-logs')) active @endif" href="{{ route('backend.cloud-files.activity-logs', ['cloudFile' => $file->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $file->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
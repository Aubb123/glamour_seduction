<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-folders.show')) active @endif" href="{{ route('backend.cloud-folders.show', ['cloudFolder' => $folder->uuid]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-folders.files')) active @endif" href="{{ route('backend.cloud-folders.files', ['cloudFolder' => $folder->uuid]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Fichiers') }}
            <span class="badge bg-primary ms-1">{{ $folder->files_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-folders.subfolders')) active @endif" href="{{ route('backend.cloud-folders.subfolders', ['cloudFolder' => $folder->uuid]) }}">
            <i class="bx bx-folder me-1"></i> {{ __('Sous-dossiers') }}
            <span class="badge bg-primary ms-1">{{ $folder->children_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-folders.audits')) active @endif" href="{{ route('backend.cloud-folders.audits', ['cloudFolder' => $folder->uuid]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $folder->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.cloud-folders.activity-logs')) active @endif" href="{{ route('backend.cloud-folders.activity-logs', ['cloudFolder' => $folder->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $folder->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

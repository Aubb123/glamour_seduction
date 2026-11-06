<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.positions.show')) active @endif" href="{{ route('backend.positions.show', ['position' => $position->uuid, 'slug' => $position->active_translation?->slug ?? 'null']) }}"><i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.positions.audits')) active @endif" href="{{ route('backend.positions.audits', ['position' => $position->uuid, 'slug' => $position->active_translation?->slug ?? 'null']) }}"><i class="bx bx-file me-1"></i> {{ __('Audits') }} <span class="badge bg-primary ms-1">{{ $position->audits_count ?? 0 }}</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.positions.activity-logs')) active @endif" href="{{ route('backend.positions.activity-logs', ['position' => $position->uuid, 'slug' => $position->active_translation?->slug ?? 'null']) }}"><i class="bx bx-pulse me-1"></i> {{ __("Logs d'activité") }} <span class="badge bg-primary ms-1">{{ $position->activity_logs_count ?? 0 }}</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('backend.position-translations.trashed', ['position_uuid' => $position->uuid]) }}"><i class="bx bx-trash me-1"></i> {{ __('Corbeille des traductions') }}</a>
    </li>
</ul>

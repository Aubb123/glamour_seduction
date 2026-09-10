<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-tags.show')) active @endif" href="{{ route('backend.product-tags.show', ['productTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> Détails
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-tags.audits')) active @endif" href="{{ route('backend.product-tags.audits', ['productTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> Audits
            <span class="badge bg-primary ms-1">{{ $tag->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-tags.activity-logs')) active @endif" href="{{ route('backend.product-tags.activity-logs', ['productTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> Logs d'activité
            <span class="badge bg-primary ms-1">{{ $tag->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-attributes.show')) active @endif" href="{{ route('backend.product-attributes.show', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> Détails
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-attributes.audits')) active @endif" href="{{ route('backend.product-attributes.audits', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> Audits
            <span class="badge bg-primary ms-1">{{ $attribute->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-attributes.activity-logs')) active @endif" href="{{ route('backend.product-attributes.activity-logs', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> Logs d'activité
            <span class="badge bg-primary ms-1">{{ $attribute->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
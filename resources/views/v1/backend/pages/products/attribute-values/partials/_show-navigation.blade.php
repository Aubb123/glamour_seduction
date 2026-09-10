<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-attribute-values.show')) active @endif" href="{{ route('backend.product-attribute-values.show', ['productAttributeValue' => $value->uuid, 'slug' => $value->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> Détails
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-attribute-values.audits')) active @endif" href="{{ route('backend.product-attribute-values.audits', ['productAttributeValue' => $value->uuid, 'slug' => $value->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> Audits
            <span class="badge bg-primary ms-1">{{ $value->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-attribute-values.activity-logs')) active @endif" href="{{ route('backend.product-attribute-values.activity-logs', ['productAttributeValue' => $value->uuid, 'slug' => $value->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> Logs d'activité
            <span class="badge bg-primary ms-1">{{ $value->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
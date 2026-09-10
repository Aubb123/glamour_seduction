<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-brands.show')) active @endif" href="{{ route('backend.product-brands.show', ['productBrand' => $brand->uuid, 'slug' => $brand->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> Détails
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-brands.audits')) active @endif" href="{{ route('backend.product-brands.audits', ['productBrand' => $brand->uuid, 'slug' => $brand->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> Audits
            <span class="badge bg-primary ms-1">{{ $brand->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-brands.activity-logs')) active @endif" href="{{ route('backend.product-brands.activity-logs', ['productBrand' => $brand->uuid, 'slug' => $brand->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> Logs d'activité
            <span class="badge bg-primary ms-1">{{ $brand->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
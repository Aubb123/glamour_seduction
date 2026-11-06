<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-variants.show')) active @endif" href="{{ route('backend.product-variants.show', ['productVariant' => $variant->uuid]) }}">
            <i class="bx bx-info-circle me-1"></i> Détails
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-variants.audits')) active @endif" href="{{ route('backend.product-variants.audits', ['productVariant' => $variant->uuid]) }}">
            <i class="bx bx-file me-1"></i> Audits
            <span class="badge bg-primary ms-1">{{ $variant->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-variants.activity-logs')) active @endif" href="{{ route('backend.product-variants.activity-logs', ['productVariant' => $variant->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> Logs d'activité
            <span class="badge bg-primary ms-1">{{ $variant->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

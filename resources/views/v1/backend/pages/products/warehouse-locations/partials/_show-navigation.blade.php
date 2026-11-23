
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouse-locations.show')) active @endif" href="{{ route('backend.product-warehouse-locations.show', ['productWarehouseLocation' => $location->uuid]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouse-locations.audits')) active @endif" href="{{ route('backend.product-warehouse-locations.audits', ['productWarehouseLocation' => $location->uuid]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $location->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouse-locations.activity-logs')) active @endif" href="{{ route('backend.product-warehouse-locations.activity-logs', ['productWarehouseLocation' => $location->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $location->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

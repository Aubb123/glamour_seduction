<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouse-location-stocks.show')) active @endif" href="{{ route('backend.product-warehouse-location-stocks.show', ['productWarehouseLocationStock' => $stock->uuid]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouse-location-stocks.audits')) active @endif" href="{{ route('backend.product-warehouse-location-stocks.audits', ['productWarehouseLocationStock' => $stock->uuid]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $stock->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouse-location-stocks.activity-logs')) active @endif" href="{{ route('backend.product-warehouse-location-stocks.activity-logs', ['productWarehouseLocationStock' => $stock->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __("Logs d'activité") }}
            <span class="badge bg-primary ms-1">{{ $stock->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
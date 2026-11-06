<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouses.show')) active @endif" href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid]) }}">
            <i class="bx bx-info-circle me-1"></i> Détails
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouses.audits')) active @endif" href="{{ route('backend.product-warehouses.audits', ['productWarehouse' => $warehouse->uuid]) }}">
            <i class="bx bx-file me-1"></i> Audits
            <span class="badge bg-primary ms-1">{{ $warehouse->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouses.activity-logs')) active @endif" href="{{ route('backend.product-warehouses.activity-logs', ['productWarehouse' => $warehouse->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> Logs d'activité
            <span class="badge bg-primary ms-1">{{ $warehouse->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouses.show')) active @endif" href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid]) }}#personnel">
            <i class="bx bx-group me-1"></i> Personnel
            <span class="badge bg-primary ms-1">{{ $warehouse->warehouse_users_count ?? 0 }}</span>
        </a>
    </li>
</ul>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouses.show')) active @endif" href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid, 'slug' => $warehouse->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouses.audits')) active @endif" href="{{ route('backend.product-warehouses.audits', ['productWarehouse' => $warehouse->uuid, 'slug' => $warehouse->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $warehouse->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouses.activity-logs')) active @endif" href="{{ route('backend.product-warehouses.activity-logs', ['productWarehouse' => $warehouse->uuid, 'slug' => $warehouse->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> {{ __("Logs d'activité") }}
            <span class="badge bg-primary ms-1">{{ $warehouse->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

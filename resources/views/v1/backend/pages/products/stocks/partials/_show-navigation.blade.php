{{-- Navigation de la fiche stock : Détails / Audits / Journal d'activité.
     Reprend le design de attribute-values/partials/_show-navigation. --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouse-stocks.show')) active @endif" href="{{ route('backend.product-warehouse-stocks.show', ['productWarehouseStock' => $stock->uuid]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouse-stocks.audits')) active @endif" href="{{ route('backend.product-warehouse-stocks.audits', ['productWarehouseStock' => $stock->uuid]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $stock->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-warehouse-stocks.activity-logs')) active @endif" href="{{ route('backend.product-warehouse-stocks.activity-logs', ['productWarehouseStock' => $stock->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __("Logs d'activité") }}
            <span class="badge bg-primary ms-1">{{ $stock->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

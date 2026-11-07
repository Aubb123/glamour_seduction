{{-- Navigation de la fiche mouvement de stock : Détails / Audits / Journal d'activité.
     Reprend le design de stocks/partials/_show-navigation. --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-stock-movements.show')) active @endif" href="{{ route('backend.product-stock-movements.show', ['productStockMovement' => $movement->uuid]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-stock-movements.audits')) active @endif" href="{{ route('backend.product-stock-movements.audits', ['productStockMovement' => $movement->uuid]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $movement->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-stock-movements.activity-logs')) active @endif" href="{{ route('backend.product-stock-movements.activity-logs', ['productStockMovement' => $movement->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __("Logs d'activité") }}
            <span class="badge bg-primary ms-1">{{ $movement->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

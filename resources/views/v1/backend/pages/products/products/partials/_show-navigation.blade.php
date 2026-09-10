<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.products.show')) active @endif" href="{{ route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> Détails
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.products.audits')) active @endif" href="{{ route('backend.products.audits', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> Audits
            <span class="badge bg-primary ms-1">{{ $product->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.products.activity-logs')) active @endif" href="{{ route('backend.products.activity-logs', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> Logs d'activité
            <span class="badge bg-primary ms-1">{{ $product->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
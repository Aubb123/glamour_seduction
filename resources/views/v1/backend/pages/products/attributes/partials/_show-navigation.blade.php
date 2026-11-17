<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-attributes.show')) active @endif" href="{{ route('backend.product-attributes.show', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-attributes.values')) active @endif" href="{{ route('backend.product-attributes.values', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-list-details me-1"></i> {{ __('Valeurs') }}
            <span class="badge bg-primary ms-1">{{ $attribute->values_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-attributes.products')) active @endif" href="{{ route('backend.product-attributes.products', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-cube me-1"></i> {{ __('Produits') }}
            <span class="badge bg-primary ms-1">{{ $attribute->products_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-attributes.audits')) active @endif" href="{{ route('backend.product-attributes.audits', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $attribute->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-attributes.activity-logs')) active @endif" href="{{ route('backend.product-attributes.activity-logs', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $attribute->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.shops.show')) active @endif" href="{{ route('backend.shops.show', ['shop' => $shop->uuid, 'slug' => $shop->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.shops.audits')) active @endif" href="{{ route('backend.shops.audits', ['shop' => $shop->uuid, 'slug' => $shop->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $shop->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.shops.activity-logs')) active @endif" href="{{ route('backend.shops.activity-logs', ['shop' => $shop->uuid, 'slug' => $shop->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> {{ __("Logs d'activité") }}
            <span class="badge bg-primary ms-1">{{ $shop->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.shops.show')) active @endif" href="{{ route('backend.shops.show', ['shop' => $shop->uuid, 'slug' => $shop->active_translation?->slug ?? 'null']) }}#personnel">
            <i class="bx bx-group me-1"></i> {{ __('Personnel') }}
            <span class="badge bg-primary ms-1">{{ $shop->shop_users_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('backend.shop-translations.trashed', ['shop_uuid' => $shop->uuid]) }}">
            <i class="bx bx-trash me-1"></i> {{ __('Corbeille des traductions') }}
        </a>
    </li>
</ul>

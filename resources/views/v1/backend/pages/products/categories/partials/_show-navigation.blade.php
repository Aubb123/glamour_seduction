<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-categories.show')) active @endif" href="{{ route('backend.product-categories.show', ['productCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-categories.audits')) active @endif" href="{{ route('backend.product-categories.audits', ['productCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $category->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.product-categories.activity-logs')) active @endif" href="{{ route('backend.product-categories.activity-logs', ['productCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $category->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

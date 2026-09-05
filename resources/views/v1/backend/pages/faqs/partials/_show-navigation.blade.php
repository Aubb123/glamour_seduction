<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.faqs.show')) active @endif" href="{{ route('backend.faqs.show', ['faq' => $faq->uuid, 'slug' => $faq->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('v1/backend/views/pages/faqs/partials/_show-navigation.details') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.faqs.audits')) active @endif" href="{{ route('backend.faqs.audits', ['faq' => $faq->uuid, 'slug' => $faq->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('v1/backend/views/pages/faqs/partials/_show-navigation.audits') }}
            <span class="badge bg-primary ms-1">{{ $faq->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.faqs.activity-logs')) active @endif" href="{{ route('backend.faqs.activity-logs', ['faq' => $faq->uuid, 'slug' => $faq->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('v1/backend/views/pages/faqs/partials/_show-navigation.logsActivite') }}
            <span class="badge bg-primary ms-1">{{ $faq->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
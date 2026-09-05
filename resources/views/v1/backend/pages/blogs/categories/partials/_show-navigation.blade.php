<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-categories.show')) active @endif" href="{{ route('backend.blog-categories.show', ['blogCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-categories.posts')) active @endif" href="{{ route('backend.blog-categories.posts', ['blogCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-book me-1"></i> {{ __('Articles') }}
            <span class="badge bg-primary ms-1">{{ $category->posts_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-categories.audits')) active @endif" href="{{ route('backend.blog-categories.audits', ['blogCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $category->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-categories.activity-logs')) active @endif" href="{{ route('backend.blog-categories.activity-logs', ['blogCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $category->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

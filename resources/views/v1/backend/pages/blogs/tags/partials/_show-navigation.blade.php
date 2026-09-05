<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-tags.show')) active @endif" href="{{ route('backend.blog-tags.show', ['blogTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-tags.posts')) active @endif" href="{{ route('backend.blog-tags.posts', ['blogTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('Articles') }}
            <span class="badge bg-primary ms-1">{{ $tag->posts_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-tags.audits')) active @endif" href="{{ route('backend.blog-tags.audits', ['blogTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $tag->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-tags.activity-logs')) active @endif" href="{{ route('backend.blog-tags.activity-logs', ['blogTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $tag->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

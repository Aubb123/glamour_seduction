<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-posts.show')) active @endif" href="{{ route('backend.blog-posts.show', ['blogPost' => $post->uuid, 'slug' => $post->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-posts.audits')) active @endif" href="{{ route('backend.blog-posts.audits', ['blogPost' => $post->uuid, 'slug' => $post->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $post->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-posts.activity-logs')) active @endif" href="{{ route('backend.blog-posts.activity-logs', ['blogPost' => $post->uuid, 'slug' => $post->active_translation?->slug ?? 'null']) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $post->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>

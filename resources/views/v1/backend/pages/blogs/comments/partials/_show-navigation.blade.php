<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-post-comments.show')) active @endif" href="{{ route('backend.blog-post-comments.show', ['blogPostComment' => $comment->uuid]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-post-comments.replies')) active @endif" href="{{ route('backend.blog-post-comments.replies', ['blogPostComment' => $comment->uuid]) }}">
            <i class="bx bx-reply me-1"></i> {{ __('Réponses') }}
            <span class="badge bg-primary ms-1">{{ $comment->replies_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-post-comments.audits')) active @endif" href="{{ route('backend.blog-post-comments.audits', ['blogPostComment' => $comment->uuid]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $comment->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-post-comments.activity-logs')) active @endif" href="{{ route('backend.blog-post-comments.activity-logs', ['blogPostComment' => $comment->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $comment->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
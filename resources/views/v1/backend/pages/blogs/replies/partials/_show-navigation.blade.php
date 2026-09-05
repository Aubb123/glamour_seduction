<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-post-comment-replies.show')) active @endif" href="{{ route('backend.blog-post-comment-replies.show', ['blogPostCommentReply' => $reply->uuid]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-post-comment-replies.audits')) active @endif" href="{{ route('backend.blog-post-comment-replies.audits', ['blogPostCommentReply' => $reply->uuid]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $reply->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.blog-post-comment-replies.activity-logs')) active @endif" href="{{ route('backend.blog-post-comment-replies.activity-logs', ['blogPostCommentReply' => $reply->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $reply->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
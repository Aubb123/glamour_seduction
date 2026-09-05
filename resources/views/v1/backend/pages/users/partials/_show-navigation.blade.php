<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.users.show')) active @endif" href="{{ route('backend.users.show', ['user' => $user->uuid, 'slug' => $user->slug]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.users.activity-logs')) active @endif" href="{{ route('backend.users.activity-logs', ['user' => $user->uuid, 'slug' => $user->slug]) }}">
            <i class="bx bx-pulse me-1"></i> {{ $user->activityLogs()->count() }} {{ __('Log d\'activités') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.users.subscribes')) active @endif" href="{{ route('backend.users.subscribes', ['user' => $user->uuid, 'slug' => $user->slug]) }}">
            <i class="bx bx-mail-send me-1"></i> {{ $user->subscribes()->count() }} {{ __('Abonnements') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.users.galleries')) active @endif" href="{{ route('backend.users.galleries', ['user' => $user->uuid, 'slug' => $user->slug]) }}">
            <i class="bx bx-image me-1"></i> {{ $user->galleries()->count() }} {{ __('Galeries') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.users.audits')) active @endif" href="{{ route('backend.users.audits', ['user' => $user->uuid, 'slug' => $user->slug]) }}">
            <i class="bx bx-file me-1"></i> {{ $user->audits()->count() }} {{ __('Audits') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.users.blog-posts')) active @endif" href="{{ route('backend.users.blog-posts', ['user' => $user->uuid, 'slug' => $user->slug]) }}">
            <i class="bx bx-book me-1"></i> {{ $user->blogPosts()->count() }} {{ __('Blogs') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.users.blog-post-comments')) active @endif" href="{{ route('backend.users.blog-post-comments', ['user' => $user->uuid, 'slug' => $user->slug]) }}">
            <i class="bx bx-message-square me-1"></i> {{ $user->blogPostComments()->count() }} {{ __('Commentaires') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.users.blog-post-comment-replies')) active @endif" href="{{ route('backend.users.blog-post-comment-replies', ['user' => $user->uuid, 'slug' => $user->slug]) }}">
            <i class="bx bx-message-rounded me-1"></i> {{ $user->blogPostCommentReplies()->count() }} {{ __('Réponses') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.users.contacts')) active @endif" href="{{ route('backend.users.contacts', ['user' => $user->uuid, 'slug' => $user->slug]) }}">
            <i class="bx bx-mail-send me-1"></i> {{ $user->contacts()->count() }} {{ __('Contacts') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.users.faqs')) active @endif" href="{{ route('backend.users.faqs', ['user' => $user->uuid, 'slug' => $user->slug]) }}">
            <i class="bx bx-help-circle me-1"></i> {{ $user->faqs()->count() }} {{ __('Faqs') }}
        </a>
    </li>
</ul>
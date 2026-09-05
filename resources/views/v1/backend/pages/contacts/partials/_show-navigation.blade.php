<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.contacts.show')) active @endif" href="{{ route('backend.contacts.show', ['contact' => $contact->uuid]) }}">
            <i class="bx bx-info-circle me-1"></i> {{ __('Détails') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.contacts.audits')) active @endif" href="{{ route('backend.contacts.audits', ['contact' => $contact->uuid]) }}">
            <i class="bx bx-file me-1"></i> {{ __('Audits') }}
            <span class="badge bg-primary ms-1">{{ $contact->audits_count ?? 0 }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('backend.contacts.activity-logs')) active @endif" href="{{ route('backend.contacts.activity-logs', ['contact' => $contact->uuid]) }}">
            <i class="bx bx-pulse me-1"></i> {{ __('Logs d\'activité') }}
            <span class="badge bg-primary ms-1">{{ $contact->activity_logs_count ?? 0 }}</span>
        </a>
    </li>
</ul>
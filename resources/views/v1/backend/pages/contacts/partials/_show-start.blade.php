<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    {{ __('Message de') }} <code>{{ $contact->fullname }}</code>
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.contacts.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                            @if (!$contact->is_read)
                                <li>
                                    <form action="{{ route('backend.contacts.mark_as_read', ['contact' => $contact->uuid]) }}" method="POST" id="read-form-{{ $contact->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('read-form-{{ $contact->uuid }}', '{{ __('Marquer ce message comme lu ?') }}', 'Marquer comme lu', 'success')">
                                            <i class="bx bx-check me-2 text-success"></i> {{ __('Marquer comme lu') }}
                                        </button>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <form action="{{ route('backend.contacts.mark_as_unread', ['contact' => $contact->uuid]) }}" method="POST" id="unread-form-{{ $contact->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('unread-form-{{ $contact->uuid }}', '{{ __('Marquer ce message comme non lu ?') }}', 'Marquer comme non lu', 'warning')">
                                            <i class="bx bx-hide me-2 text-warning"></i> {{ __('Marquer comme non lu') }}
                                        </button>
                                    </form>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('backend.contacts.destroy', ['contact' => $contact->uuid]) }}" method="POST" id="delete-form-{{ $contact->uuid }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $contact->uuid }}', '{{ __('Supprimer ce message ?') }}', 'Supprimer', 'warning')">
                                        <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                    </button>
                                </form>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>

            <div class="card-body">

                @include('v1.backend.pages.contacts.partials._show-navigation', ['contact' => $contact])
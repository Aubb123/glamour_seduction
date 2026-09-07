@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset(getEnvFolder() . 'others/backend/datatables/buttons.dataTables.min.css') }}">

    <style>
        .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    </style>
@endpush

@include('v1.backend.layouts.partials._statistics', [
    'statistics' => [
        [
            'label' => __('Contacts'),
            'value' => $contacts->count(),
            'icon'  => 'bx bx-mail-send',
            'color' => 'primary',
        ],
        [
            'label' => __('Non lus'),
            'value' => $contacts->where('is_read', false)->count(),
            'icon'  => 'bx bx-archive',
            'color' => 'warning',
        ],
        [
            'label' => __('Lus'),
            'value' => $contacts->where('is_read', true)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Audits'),
            'value' => $contacts->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'info',
        ],
        [
            'label' => __('Activités'),
            'value' => $contacts->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'danger',
        ],
        [
            'label' => __('Expéditeurs'),
            'value' => $contacts->pluck('email')->filter()->unique()->count(),
            'icon'  => 'bx bx-user',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Messages reçus') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Liste des messages'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.contacts.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.contacts.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="contacts-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Expéditeur') }}</th>
                                <th scope="col">{{ __('Email') }}</th>
                                <th scope="col">{{ __('Sujet') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs d\'activité') }}</th>
                                <th scope="col">{{ __('Reçu le') }}</th>
                                @if(request()->routeIs('backend.contacts.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contacts as $contact)
                                <tr class="{{ !$contact->is_read ? 'table-primary' : '' }}">
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        {{ $contact->fullname }}
                                        @if($contact->user)
                                            <br>
                                            <a href="{{ route('backend.users.show', ['user' => $contact->user->uuid, 'slug' => $contact->user->slug]) }}" class="text-decoration-none">
                                                <small class="text-primary">
                                                    <i class="bx bx-user me-1"></i>{{ $contact->user->first_name . ' ' . $contact->user->last_name }}
                                                </small>
                                            </a>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                    </td>

                                    <td>
                                        <code>{{ Str::limit($contact->subject ?? __('(Sans objet)'), 40) }}</code>
                                    </td>

                                    <td>
                                        @if ($contact->is_read)
                                            <span class="badge badge-soft-success">{{ __('Lu') }}</span>
                                        @else
                                            <span class="badge badge-soft-warning">{{ __('Non lu') }}</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.contacts.audits', ['contact' => $contact->uuid]) }}" class="text-decoration-none">
                                            <span class="badge bg-info">
                                                <i class="bx bx-file me-1"></i> {{ $contact->audits_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.contacts.activity-logs', ['contact' => $contact->uuid]) }}" class="text-decoration-none">
                                            <span class="badge bg-secondary">
                                                <i class="bx bx-pulse me-1"></i> {{ $contact->activity_logs_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($contact->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.contacts.trashed'))
                                        <td class="text-muted">
                                            @formatDate($contact->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($contact->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.contacts.restore', ['uuid' => $contact->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $contact->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $contact->uuid }}', '{{ __('Restaurer ce message ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.contacts.force_destroy', ['uuid' => $contact->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $contact->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $contact->uuid }}', '{{ __('Supprimer définitivement ce message ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$contact->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.contacts.show', ['contact' => $contact->uuid]) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                @if (!$contact->is_read)
                                                    <form action="{{ route('backend.contacts.mark_as_read', ['contact' => $contact->uuid]) }}" method="POST" class="d-inline" id="read-form-{{ $contact->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('read-form-{{ $contact->uuid }}', '{{ __('Marquer ce message comme lu ?') }}', 'Marquer comme lu', 'success')" title="{{ __('Marquer comme lu') }}">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.contacts.mark_as_unread', ['contact' => $contact->uuid]) }}" method="POST" class="d-inline" id="unread-form-{{ $contact->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" onclick="confirmAction('unread-form-{{ $contact->uuid }}', '{{ __('Marquer ce message comme non lu ?') }}', 'Marquer comme non lu', 'warning')" title="{{ __('Marquer comme non lu') }}">
                                                            <i class="bx bx-hide"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.contacts.destroy', ['contact' => $contact->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $contact->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $contact->uuid }}', '{{ __('Supprimer ce message ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.contacts.trashed') ? 8 : 7 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        {{ __('Aucun message trouvé.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/buttons.print.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#contacts-table').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>' +
                'tr'                                        +
                '<"row mt-3"<"col-md-5"l><"col-md-7"p>>',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: "{{ app()->getLocale() === 'fr' ? asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/fr-FR.json') : asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/en-GB.json') }}"
                },
            });
        });
    </script>
@endpush
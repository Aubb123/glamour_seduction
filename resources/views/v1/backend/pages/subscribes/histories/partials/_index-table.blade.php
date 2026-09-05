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
            'label' => __('Historiques'),
            'value' => $histories->count(),
            'icon'  => 'bx bx-time-five',
            'color' => 'primary',
        ],
        [
            'label' => __('Abonnements'),
            'value' => $histories->where('action', 'subscribed')->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Désabonnements'),
            'value' => $histories->where('action', 'unsubscribed')->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Autres actions'),
            'value' => $histories->whereNotIn('action', ['subscribed', 'unsubscribed'])->count(),
            'icon'  => 'bx bx-info-circle',
            'color' => 'info',
        ],
        [
            'label' => __('IP uniques'),
            'value' => $histories->pluck('ip_address')->filter()->unique()->count(),
            'icon'  => 'bx bx-globe',
            'color' => 'warning',
        ],
        [
            'label' => __('Abonnés liés'),
            'value' => $histories->pluck('subscribe_id')->unique()->count(),
            'icon'  => 'bx bx-user',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Historique des abonnements') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Liste des abonnés'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.subscribes.index'],
                                ['label' => __('Historique'), 'icon' => 'bx bx-time-five', 'route' => 'backend.subscribe-histories.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.subscribe-histories.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="subscribe-histories-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Email') }}</th>
                                <th scope="col">{{ __('Action') }}</th>
                                <th scope="col">{{ __('IP') }}</th>
                                <th scope="col">{{ __('Effectué le') }}</th>
                                @if(request()->routeIs('backend.subscribe-histories.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($histories as $history)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><code>{{ $history->subscribe?->email ?? '—' }}</code></td>
                                    <td>
                                        <span class="badge bg-soft-{{ $history->action === 'subscribed' ? 'success' : ($history->action === 'unsubscribed' ? 'danger' : 'info') }}">
                                            {{ __(ucfirst($history->action)) }}
                                        </span>
                                    </td>
                                    <td><code>{{ $history->ip_address ?? '—' }}</code></td>
                                    <td class="text-muted">@formatDate($history->acted_at, 'D MMMM YYYY - HH:mm:ss')</td>

                                    @if(request()->routeIs('backend.subscribe-histories.trashed'))
                                        <td class="text-muted">@formatDate($history->deleted_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                    @endif

                                    <td>
                                        @if ($history->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.subscribe-histories.restore', ['uuid' => $history->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $history->uuid }}">
                                                    @csrf @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $history->uuid }}', '{{ __('Restaurer cet historique ?') }}', 'Restaurer', 'success')"><i class="bx bx-undo"></i></button>
                                                </form>
                                                <form action="{{ route('backend.subscribe-histories.force_destroy', ['uuid' => $history->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $history->uuid }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $history->uuid }}', '{{ __('Supprimer définitivement cet historique ? Cette action est irréversible.') }}')"><i class="bx bx-trash"></i></button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.subscribe-histories.show', ['subscribeHistory' => $history->uuid]) }}" class="btn btn-sm btn-info" title="{{ __('Voir') }}"><i class="bx bx-show"></i></a>

                                                <form action="{{ route('backend.subscribe-histories.destroy', ['subscribeHistory' => $history->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $history->uuid }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction('delete-form-{{ $history->uuid }}', '{{ __('Supprimer cet historique ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}"><i class="bx bx-trash"></i></button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.subscribe-histories.trashed') ? 7 : 6 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucun historique trouvé.') }}
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
            $('#subscribe-histories-table').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>tr<"row mt-3"<"col-md-5"l><"col-md-7"p>>',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    url: "{{ app()->getLocale() === 'fr' ? asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/fr-FR.json') : asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/en-GB.json') }}"
                },
            });
        });
    </script>
@endpush
@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset(getEnvFolder() . 'others/backend/datatables/buttons.dataTables.min.css') }}">

    <!-- {{-- ... tes styles datatables existants ... --}} -->
    <style>
        .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    </style>

@endpush

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Platform versions</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    
                    <!-- Menu des actions -->
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Créer un nouveau', 'icon' => 'bx bx-plus', 'route' => 'backend.versions.create'],
                                ['label' => 'Liste des versions', 'icon' => 'bx bx-list-ul', 'route' => 'backend.versions.index'],
                                ['label' => 'Corbeille', 'icon' => 'bx bx-trash', 'route' => 'backend.versions.trashed'],
                            ]
                        ])
                    </ul>
                    
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="versions-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Slug') }}</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Actif') }}</th>
                                <th scope="col">{{ __('Publié') }}</th>
                                <th scope="col">{{ __('Lancé le') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs d\'activité') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                <th scope="col">{{ __('Modifié le') }}</th>
                                @if(request()->routeIs('backend.versions.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($versions as $version)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="fw-bold">
                                        {{ $version->code }}
                                    </td>

                                    <td class="fw-bold">
                                        {{ $version->active_translation?->name ?? '—' }}
                                    </td>

                                    <td>
                                        <span class="{{ $version->status?->badgeClass() ?? 'badge bg-secondary-subtle text-secondary' }}">
                                            {{ $version->status?->label() ?? __('Inconnu') }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($version->is_published)
                                            <span class="badge bg-success-subtle text-success">{{ __('Publié') }}</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">{{ __('Brouillon') }}</span>
                                        @endif
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($version->released_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.versions.audits', ['platformVersion' => $version->uuid, 'slug' => $version->active_translation?->slug ?? $version->code]) }}" class="btn btn-sm btn-info" title="{{ __('Voir les audits') }}">
                                            <i class="bx bx-file"></i>
                                            <span class="badge ms-1">{{ $version->audits_count ?? 0 }}</span>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.versions.activity-logs', ['platformVersion' => $version->uuid, 'slug' => $version->active_translation?->slug ?? $version->code]) }}" class="btn btn-sm btn-info" title="{{ __('Voir les logs d\'activité') }}">
                                            <i class="bx bx-pulse"></i>
                                            <span class="badge ms-1">{{ $version->activity_logs_count ?? 0 }}</span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($version->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($version->updated_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.versions.trashed'))
                                        <td class="text-muted">
                                            @formatDate($version->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($version->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <form action="{{ route('backend.versions.restore', ['uuid' => $version->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $version->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $version->uuid }}', '{{ __('Restaurer cette version ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.versions.force_destroy', ['uuid' => $version->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{$version->uuid}}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $version->uuid }}', 'Supprimer définitivement cette version ? Cette action est irréversible.')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        @endif

                                        @if(!$version->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <a href="{{ route('backend.versions.show', ['platformVersion' => $version->uuid, 'slug' => $version->active_translation?->slug ?? $version->code]) }}" class="btn btn-sm btn-info" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.versions.edit', ['platformVersion' => $version->uuid]) }}" class="btn btn-sm btn-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                <form action="{{ route('backend.versions.destroy', ['platformVersion' => $version->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{$version->uuid}}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"  class="btn btn-sm btn-danger"  title="{{ __('Supprimer') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{$version->uuid}}', 'Êtes-vous sûr de vouloir supprimer cette version ?')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucune version trouvée.') }}
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
            $('#versions-table').DataTable({
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
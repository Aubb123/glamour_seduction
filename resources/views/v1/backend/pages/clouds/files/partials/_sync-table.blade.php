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
            'label' => __('Fichiers non synchronisés'),
            'value' => $files->count(),
            'icon'  => 'bx bx-cloud',
            'color' => 'warning',
        ],
        [
            'label' => __('Taille totale'),
            'value' => $files->sum('file_size') > 0
                ? number_format($files->sum('file_size') / 1024 / 1024, 2) . ' Mo'
                : '0 Mo',
            'icon'  => 'bx bx-data',
            'color' => 'primary',
        ],
        [
            'label' => __('En attente'),
            'value' => $files->where('sync_status', \App\Enums\Clouds\Files\CloudFileStatus::Pending)->count(),
            'icon'  => 'bx bx-time-five',
            'color' => 'info',
        ],
        [
            'label' => __('En erreur'),
            'value' => $files->where('sync_status', \App\Enums\Clouds\Files\CloudFileStatus::Error)->count(),
            'icon'  => 'bx bx-error-circle',
            'color' => 'danger',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Synchronisation Cloud') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Liste des fichiers'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.cloud-files.index'],
                                ['label' => __('Synchronisation Cloud'), 'icon' => 'bx bx-cloud-upload', 'route' => 'backend.cloud-files.sync'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.cloud-files.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('backend.cloud-files.sync-selected') }}" method="POST" id="sync-form">
                    @csrf

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <p class="text-muted mb-0">
                            {{ __('Cochez les fichiers à synchroniser vers Backblaze B2, puis cliquez sur Synchroniser.') }}
                        </p>
                        <button type="submit" class="btn btn-success" id="sync-selected-btn" disabled>
                            <i class="bx bx-cloud-upload me-1"></i> {{ __('Synchroniser la sélection') }}
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered text-nowrap" id="cloud-files-sync-table">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <input type="checkbox" id="check-all" class="form-check-input">
                                    </th>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ __('Nom du fichier') }}</th>
                                    <th scope="col">{{ __('Type') }}</th>
                                    <th scope="col">{{ __('Statut') }}</th>
                                    <th scope="col">{{ __('Taille') }}</th>
                                    <th scope="col">{{ __('Dossier') }}</th>
                                    <th scope="col">{{ __('Entité associée') }}</th>
                                    <th scope="col">{{ __('Chemin local') }}</th>
                                    <th scope="col">{{ __('Créé le') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($files as $file)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="file_uuids[]" value="{{ $file->uuid }}" class="form-check-input row-checkbox">
                                        </td>
                                        <td>{{ $loop->iteration }}</td>

                                        <td class="fw-bold">
                                            <code>{{ $file->file_name }}</code>
                                        </td>

                                        <td>
                                            <span class="badge {{ $file->file_type_badge }}">{{ $file->file_type_label }}</span>
                                        </td>

                                        <td>
                                            @if($file->sync_status === \App\Enums\Clouds\Files\CloudFileStatus::Pending)
                                                <span class="badge badge-soft-warning">
                                                    <i class="bx bx-time-five me-1"></i> {{ __('En attente') }}
                                                </span>
                                            @elseif($file->sync_status === \App\Enums\Clouds\Files\CloudFileStatus::Processing)
                                                <span class="badge badge-soft-info">
                                                    <i class="bx bx-refresh me-1"></i> {{ __('En cours') }}
                                                </span>
                                            @elseif($file->sync_status === \App\Enums\Clouds\Files\CloudFileStatus::Error)
                                                <span class="badge badge-soft-danger">
                                                    <i class="bx bx-error-circle me-1"></i> {{ __('Erreur') }}
                                                </span>
                                            @else
                                                <span class="badge badge-soft-success">
                                                    <i class="bx bx-check-circle me-1"></i> {{ __('Synchronisé') }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-muted">{{ $file->formatted_size }}</td>

                                        <td>
                                            @if($file->cloudFolder)
                                                <span class="badge badge-soft-primary">
                                                    <i class="bx bx-folder me-1"></i> {{ $file->cloudFolder->folder_name }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($file->fileable)
                                                <span class="badge badge-soft-secondary">{{ class_basename($file->fileable_type) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($file->local_path)
                                                <code class="text-muted">{{ Str::limit($file->local_path, 30) }}</code>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        <td class="text-muted">
                                            @formatDate($file->created_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-5">
                                            <i class="bx bx-check-circle fs-3 d-block mb-2 text-success"></i>
                                            {{ __('Tous les fichiers sont synchronisés.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
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
            var table = $('#cloud-files-sync-table').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>' +
                'tr'                                        +
                '<"row mt-3"<"col-md-5"l><"col-md-7"p>>',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: "{{ app()->getLocale() === 'fr' ? asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/fr-FR.json') : asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/en-GB.json') }}"
                },
                columnDefs: [
                    { orderable: false, targets: [0] },
                ],
            });

            // Check-all / Uncheck-all
            $('#check-all').on('change', function() {
                $('.row-checkbox').prop('checked', this.checked);
                updateSyncButton();
            });

            // Mise à jour du bouton selon les cases cochées
            $('.row-checkbox').on('change', updateSyncButton);

            function updateSyncButton() {
                var count = $('.row-checkbox:checked').length;
                $('#sync-selected-btn').prop('disabled', count === 0);
                $('#sync-selected-btn span').remove();
                $('#sync-selected-btn').html('<i class="bx bx-cloud-upload me-1"></i> {{ __("Synchroniser la sélection") }} (' + count + ')');
            }
        });
    </script>
@endpush
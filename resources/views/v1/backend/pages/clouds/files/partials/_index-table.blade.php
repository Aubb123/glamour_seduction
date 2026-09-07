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
            'label' => __('Fichiers'),
            'value' => $files->count(),
            'icon'  => 'bx bx-file',
            'color' => 'primary',
        ],
        [
            'label' => __('Taille totale'),
            'value' => $files->sum('file_size') > 0
                ? number_format($files->sum('file_size') / 1024 / 1024, 2) . ' Mo'
                : '0 Mo',
            'icon'  => 'bx bx-data',
            'color' => 'success',
        ],
        [
            'label' => __('Types'),
            'value' => $files->pluck('file_type')->filter()->unique()->count(),
            'icon'  => 'bx bx-tag',
            'color' => 'info',
        ],
        [
            'label' => __('Dossiers'),
            'value' => $files->pluck('cloud_folder_id')->filter()->unique()->count(),
            'icon'  => 'bx bx-folder',
            'color' => 'warning',
        ],
        [
            'label' => __('Audits'),
            'value' => $files->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'danger',
        ],
        [
            'label' => __('Activités'),
            'value' => $files->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Fichiers Cloud') }}</h5>
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
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="cloud-files-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Nom du fichier') }}</th>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Taille') }}</th>
                                <th scope="col">{{ __('Dossier') }}</th>
                                <th scope="col">{{ __('Entité associée') }}</th>
                                <th scope="col">{{ __('Chemin local') }}</th>
                                <th scope="col">{{ __('Dernière tentative') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs d\'activité') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if(request()->routeIs('backend.cloud-files.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                    <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($files as $file)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        <img src="{{ route('cloud-file.show', ['uuid' => $file->uuid]) }}" alt="{{ $file->file_name }}" class="me-2" style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%;">
                                        <code>{{ Str::limit($file->file_name, 9) }}</code>
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
                                            <a href="{{ route('backend.cloud-folders.show', ['cloudFolder' => $file->cloudFolder->uuid]) }}"
                                               class="text-decoration-none">
                                                <span class="badge badge-soft-primary">
                                                    <i class="bx bx-folder me-1"></i> {{ $file->cloudFolder->folder_name }}
                                                </span>
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($file->fileable)
                                            @if($file->fileable instanceof \App\Models\User)
                                                <a href="{{ route('backend.users.show', ['user' => $file->fileable->uuid, 'slug' => $file->fileable->slug]) }}"
                                                   class="text-decoration-none">
                                                    <span class="badge bg-info">
                                                        <i class="bx bx-user me-1"></i> User: {{ $file->fileable->first_name . ' ' . $file->fileable->last_name }}
                                                    </span>
                                                </a>
                                            @elseif($file->fileable instanceof \App\Models\BlogPost)
                                                <a href="{{ route('backend.blog-posts.show', ['blogPost' => $file->fileable->uuid, 'slug' => $file->fileable->active_translation?->slug ?? 'null']) }}"
                                                   class="text-decoration-none">
                                                    <span class="badge bg-secondary">
                                                        <i class="bx bx-book me-1"></i> Post: {{ Str::limit($file->fileable->active_translation?->title ?? 'Article', 30) }}
                                                    </span>
                                                </a>
                                            @elseif($file->fileable instanceof \App\Models\BlogCategory)
                                                <a href="{{ route('backend.blog-categories.show', ['blogCategory' => $file->fileable->uuid, 'slug' => $file->fileable->active_translation?->slug ?? 'null']) }}"
                                                   class="text-decoration-none">
                                                    <span class="badge bg-warning">
                                                        <i class="bx bx-folder me-1"></i> Category: {{ Str::limit($file->fileable->active_translation?->name ?? 'Catégorie', 30) }}
                                                    </span>
                                                </a>
                                            @elseif($file->fileable instanceof \App\Models\BlogTag)
                                                <a href="{{ route('backend.blog-tags.show', ['blogTag' => $file->fileable->uuid, 'slug' => $file->fileable->active_translation?->slug ?? 'null']) }}"
                                                   class="text-decoration-none">
                                                    <span class="badge bg-danger">
                                                        <i class="bx bx-tag me-1"></i> Tag: {{ Str::limit($file->fileable->active_translation?->name ?? 'Tag', 30) }}
                                                    </span>
                                                </a>
                                            @elseif($file->fileable instanceof \App\Models\Gallery)
                                                <a href="{{ route('backend.galleries.show', ['gallery' => $file->fileable->uuid, 'slug' => $file->fileable->active_translation?->slug ?? 'null']) }}"
                                                   class="text-decoration-none">
                                                    <span class="badge bg-success">
                                                        <i class="bx bx-image me-1"></i> Gallery: {{ Str::limit($file->fileable->active_translation?->name ?? 'Galerie', 30) }}
                                                    </span>
                                                </a>
                                            @else
                                                <span class="badge badge-soft-secondary">{{ class_basename($file->fileable_type) }}</span>
                                            @endif
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
                                        @if($file->last_sync_attempt_at)
                                            @formatDate($file->last_sync_attempt_at, 'D MMMM YYYY - HH:mm:ss')
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.cloud-files.audits', ['cloudFile' => $file->uuid]) }}" class="text-decoration-none">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-file me-1"></i> {{ $file->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.cloud-files.activity-logs', ['cloudFile' => $file->uuid]) }}" class="text-decoration-none">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-pulse me-1"></i> {{ $file->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($file->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>
                                    
                                    @if(request()->routeIs('backend.cloud-files.trashed'))
                                        <td class="text-muted">
                                            @formatDate($file->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if($file->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.cloud-files.restore', ['uuid' => $file->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $file->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $file->uuid }}', '{{ __('Restaurer ce fichier ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.cloud-files.force_destroy', ['uuid' => $file->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $file->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $file->uuid }}', '{{ __('Supprimer définitivement ce fichier ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$file->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.cloud-files.show', ['cloudFile' => $file->uuid]) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <form action="{{ route('backend.cloud-files.destroy', ['cloudFile' => $file->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $file->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $file->uuid }}', '{{ __('Supprimer ce fichier ?') }}', '{{ __('Supprimer') }}', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucun fichier trouvé.') }}
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
            $('#cloud-files-table').DataTable({
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
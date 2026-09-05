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
            'label' => __('Galeries'),
            'value' => $galleries->count(),
            'icon'  => 'bx bx-image',
            'color' => 'primary',
        ],
        [
            'label' => __('Actives'),
            'value' => $galleries->where('status', \App\Enums\Galleries\GalleryStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Inactives'),
            'value' => $galleries->where('status', \App\Enums\Galleries\GalleryStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Fichiers'),
            'value' => $galleries->sum(fn ($gallery) => $gallery->cloudFiles->count()),
            'icon'  => 'bx bx-file',
            'color' => 'info',
        ],
        [
            'label' => __('Audits'),
            'value' => $galleries->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => __('Activités'),
            'value' => $galleries->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Galeries') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Créer une galerie'), 'icon' => 'bx bx-plus', 'route' => 'backend.galleries.create'],
                                ['label' => __('Liste des galeries'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.galleries.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.galleries.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="galleries-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Titre') }}</th>
                                <th scope="col">{{ __('Auteur') }}</th>
                                <th scope="col">{{ __('Ordre') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs d\'activité') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if(request()->routeIs('backend.galleries.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($galleries as $gallery)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        @php $galleryImage = $gallery->getFirstImage(); @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ $galleryImage['url_img'] }}" target="_blank" rel="noopener noreferrer">
                                                <img src="{{ $galleryImage['url_img'] }}" alt="{{ $gallery->active_translation?->title ?? __('Image gallery') }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;">
                                            </a>
                                            <a href="{{ route('backend.galleries.show', ['gallery' => $gallery->uuid, 'slug' => $gallery->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-nowrap">
                                                {{ Str::limit($gallery->active_translation?->title ?? '-', 50) }}
                                            </a>
                                        </div>
                                    </td>

                                    <td class="text-muted">
                                        @if($gallery->user)
                                            <a href="{{ route('backend.users.show', ['user' => $gallery->user->uuid, 'slug' => $gallery->user->slug]) }}"
                                                class="fw-semibold text-primary text-decoration-none">
                                                {{ $gallery->user->first_name . ' ' . $gallery->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-secondary">{{ $gallery->sort_order }}</span>
                                    </td>

                                    <td>
                                        <span class="{{ $gallery->status?->badgeClass() ?? 'badge bg-soft-secondary' }}">
                                            {{ $gallery->status?->label() ?? __('Inconnu') }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.galleries.audits', ['gallery' => $gallery->uuid, 'slug' => $gallery->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-muted" title="{{ __('Voir les audits') }}">
                                            <span class="badge bg-soft-info">
                                                <i class="bx bx-file me-1"></i>{{ $gallery->audits_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.galleries.activity-logs', ['gallery' => $gallery->uuid, 'slug' => $gallery->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-muted" title="{{ __('Voir les logs d\'activité') }}">
                                            <span class="badge bg-soft-warning">
                                                <i class="bx bx-pulse me-1"></i>{{ $gallery->activity_logs_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($gallery->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.galleries.trashed'))
                                        <td class="text-muted">
                                            @formatDate($gallery->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($gallery->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.galleries.restore', ['uuid' => $gallery->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $gallery->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $gallery->uuid }}', '{{ __('Restaurer cette galerie ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.galleries.force_destroy', ['uuid' => $gallery->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $gallery->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $gallery->uuid }}', '{{ __('Supprimer définitivement cette galerie ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$gallery->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.galleries.show', ['gallery' => $gallery->uuid, 'slug' => $gallery->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-info" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.galleries.edit', ['gallery' => $gallery->uuid]) }}" class="btn btn-sm btn-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                <form action="{{ route('backend.galleries.destroy', ['gallery' => $gallery->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $gallery->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction('delete-form-{{ $gallery->uuid }}', '{{ __('Supprimer cette galerie ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.galleries.trashed') ? 8 : 7 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucune galerie trouvée.') }}
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
            $('#galleries-table').DataTable({
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
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
            'label' => __('Tags'),
            'value' => $tags->count(),
            'icon'  => 'bx bx-tag',
            'color' => 'primary',
        ],
        [
            'label' => __('Articles'),
            'value' => $tags->sum('posts_count'),
            'icon'  => 'bx bx-book',
            'color' => 'success',
        ],
        [
            'label' => __('Actifs'),
            'value' => $tags->where('status', \App\Enums\Blogs\Tags\BlogTagStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'info',
        ],
        [
            'label' => __('Inactifs'),
            'value' => $tags->where('status', \App\Enums\Blogs\Tags\BlogTagStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Audits'),
            'value' => $tags->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => __('Activités'),
            'value' => $tags->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Tags de blog</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Créer un tag', 'icon' => 'bx bx-plus', 'route' => 'backend.blog-tags.create'],
                                ['label' => 'Liste des tags', 'icon' => 'bx bx-list-ul', 'route' => 'backend.blog-tags.index'],
                                ['label' => 'Corbeille', 'icon' => 'bx bx-trash', 'route' => 'backend.blog-tags.trashed'],
                            ]
                        ])
                    </ul>
                    
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="blog-tags-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Slug') }}</th>
                                <th scope="col">{{ __('Articles') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs') }}</th>
                                <th scope="col">{{ __('Ordre') }}</th>
                                <th scope="col">{{ __('Actif') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if(request()->routeIs('backend.blog-tags.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tags as $tag)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        {{ $tag->active_translation?->name ?? '-' }}
                                    </td>

                                    <td>
                                        <code>{{ $tag->active_translation?->slug ?? 'null' }}</code>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.blog-tags.posts', ['blogTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-book me-1"></i> {{ $tag->posts_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.blog-tags.audits', ['blogTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $tag->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.blog-tags.activity-logs', ['blogTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-info">
                                                <i class="bx bx-pulse me-1"></i> {{ $tag->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>{{ $tag->sort_order }}</td>

                                    <td>
                                        <span class="{{ $tag->status?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $tag->status?->label() ?? __('Inconnu') }}
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($tag->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.blog-tags.trashed'))
                                        <td class="text-muted">
                                            @formatDate($tag->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($tag->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.blog-tags.restore', ['uuid' => $tag->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $tag->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $tag->uuid }}', '{{ __('Restaurer ce tag ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.blog-tags.force_destroy', ['uuid' => $tag->uuid]) }}" method="POST" style="display:inline-block" id="force-destroy-form-{{ $tag->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmAction('force-destroy-form-{{ $tag->uuid }}', '{{ __('Supprimer définitivement ce tag ? Cette action est irréversible.') }}', 'Supprimer définitivement', 'danger')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$tag->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.blog-tags.show', ['blogTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.blog-tags.edit', ['blogTag' => $tag->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($tag->status === \App\Enums\Blogs\Tags\BlogTagStatus::Active)
                                                    <form action="{{ route('backend.blog-tags.deactivate', ['blogTag' => $tag->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $tag->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" onclick="confirmAction('deactivate-form-{{ $tag->uuid }}', '{{ __('Désactiver ce tag ?') }}', 'Désactiver', 'warning')" title="{{ __('Désactiver') }}">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.blog-tags.activate', ['blogTag' => $tag->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $tag->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('activate-form-{{ $tag->uuid }}', '{{ __('Activer ce tag ?') }}', 'Activer', 'success')" title="{{ __('Activer') }}">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.blog-tags.destroy', ['blogTag' => $tag->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $tag->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $tag->uuid }}', '{{ __('Supprimer ce tag ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.blog-tags.trashed') ? 11 : 10 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        {{ __('Aucun tag trouvé.') }}
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
            $('#blog-tags-table').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>' +
                    '<"row"<"col-md-12"tr>>'                  +
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

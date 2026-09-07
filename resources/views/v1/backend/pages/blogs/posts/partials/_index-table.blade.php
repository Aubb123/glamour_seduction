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
            'label' => __('Articles'),
            'value' => $posts->count(),
            'icon'  => 'bx bx-book',
            'color' => 'primary',
        ],
        [
            'label' => __('Publiés'),
            'value' => $posts->where('status', \App\Enums\Blogs\Posts\BlogPostStatus::Published)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Brouillons'),
            'value' => $posts->where('status', \App\Enums\Blogs\Posts\BlogPostStatus::Draft)->count(),
            'icon'  => 'bx bx-edit',
            'color' => 'warning',
        ],
        [
            'label' => __('Vues totales'),
            'value' => $posts->sum('views'),
            'icon'  => 'bx bx-show',
            'color' => 'info',
        ],
        [
            'label' => __('Audits'),
            'value' => $posts->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'danger',
        ],
        [
            'label' => __('Activités'),
            'value' => $posts->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Articles de blog</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Créer un article', 'icon' => 'bx bx-plus', 'route' => 'backend.blog-posts.create'],
                                ['label' => 'Liste des articles', 'icon' => 'bx bx-list-ul', 'route' => 'backend.blog-posts.index'],
                                ['label' => 'Corbeille', 'icon' => 'bx bx-trash', 'route' => 'backend.blog-posts.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="blog-posts-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Titre') }}</th>
                                <th scope="col">{{ __('Catégorie') }}</th>
                                <th scope="col">{{ __('Auteur') }}</th>
                                <th scope="col">{{ __('Vues') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs') }}</th>
                                <th scope="col">{{ __('Ordre') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if(request()->routeIs('backend.blog-posts.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                             @forelse ($posts as $post)
                                @php($image = $post->getFirstImage())
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ $image['url_img'] }}" target="_blank" rel="noopener noreferrer">
                                                <img src="{{ $image['url_img'] }}" alt="{{ $post->active_translation?->title ?? __('Image post') }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;">
                                            </a>
                                            <a href="{{ route('backend.blog-posts.show', ['blogPost' => $post->uuid, 'slug' => $post->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-nowrap">
                                                {{ Str::limit($post->active_translation?->title ?? __('Sans titre'), 25) }}
                                            </a>
                                        </div>
                                    </td>

                                    <td>
                                        @if($post->category)
                                            <a href="{{ route('backend.blog-categories.show', ['blogCategory' => $post->category->uuid, 'slug' => $post->category?->active_translation?->slug ?? 'null']) }}"
                                               class="badge badge-soft-primary text-decoration-none">
                                                {{ $post->category?->active_translation?->name ?? '—' }}
                                            </a>
                                        @else
                                            <span class="badge badge-soft-primary">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($post->user)
                                            <a href="{{ route('backend.users.show', ['user' => $post->user->uuid, 'slug' => $post->user->slug]) }}"
                                               class="fw-semibold text-primary text-decoration-none">
                                                {{ $post->user->first_name . ' ' . $post->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('Anonyme') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-info">{{ $post->views }}</span>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.blog-posts.audits', ['blogPost' => $post->uuid, 'slug' => $post->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $post->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.blog-posts.activity-logs', ['blogPost' => $post->uuid, 'slug' => $post->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-info">
                                                <i class="bx bx-pulse me-1"></i> {{ $post->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-secondary">{{ $post->sort_order }}</span>
                                    </td>

                                    <td>
                                        <span class="{{ $post->status?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $post->status?->label() ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($post->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.blog-posts.trashed'))
                                        <td class="text-muted">
                                            @formatDate($post->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($post->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.blog-posts.restore', ['uuid' => $post->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $post->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $post->uuid }}', '{{ __('Restaurer cet article ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.blog-posts.force_destroy', ['uuid' => $post->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $post->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $post->uuid }}', '{{ __('Supprimer définitivement cet article ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$post->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.blog-posts.show', ['blogPost' => $post->uuid, 'slug' => $post->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.blog-posts.edit', ['blogPost' => $post->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                <form action="{{ route('backend.blog-posts.destroy', ['blogPost' => $post->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $post->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $post->uuid }}', '{{ __('Supprimer cet article ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.blog-posts.trashed') ? 12 : 11 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        {{ __('Aucun article trouvé.') }}
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
            $('#blog-posts-table').DataTable({
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
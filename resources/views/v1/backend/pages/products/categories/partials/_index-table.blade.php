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
            'label' => 'Catégories',
            'value' => $categories->count(),
            'icon'  => 'bx bx-folder',
            'color' => 'primary',
        ],
        [
            'label' => 'Sous-catégories',
            'value' => $categories->sum('children_count'),
            'icon'  => 'bx bx-collection',
            'color' => 'success',
        ],
        [
            'label' => 'Actives',
            'value' => $categories->where('status', \App\Enums\Products\Categories\ProductCategoryStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'info',
        ],
        [
            'label' => 'Inactives',
            'value' => $categories->where('status', \App\Enums\Products\Categories\ProductCategoryStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => 'Audits',
            'value' => $categories->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => 'Activités',
            'value' => $categories->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Catégories de produits</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Créer une catégorie', 'icon' => 'bx bx-plus', 'route' => 'backend.product-categories.create'],
                                ['label' => 'Liste des catégories', 'icon' => 'bx bx-list-ul', 'route' => 'backend.product-categories.index'],
                                ['label' => 'Corbeille', 'icon' => 'bx bx-trash', 'route' => 'backend.product-categories.trashed'],
                            ]
                        ])
                    </ul>
                    
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="product-categories-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Parent</th>
                                <th scope="col">Sous-catégories</th>
                                <th scope="col">Audits</th>
                                <th scope="col">Logs</th>
                                <th scope="col">Ordre</th>
                                <th scope="col">Actif</th>
                                <th scope="col">Créé le</th>
                                @if(request()->routeIs('backend.product-categories.trashed'))
                                    <th scope="col">Supprimé le</th>
                                @endif
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @forelse ($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ $category->getFirstImage()['url_img'] }}" ttarget="_blank" rrel="noopener noreferrer">
                                                <img src="{{ $category->getFirstImage()['url_img'] }}" alt="{{ $category->active_translation?->name ?? 'Image catégorie' }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;">
                                            </a>
                                            <a href="{{ route('backend.product-categories.show', ['productCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-nowrap">
                                                {{ Str::limit($category->active_translation?->name ?? '-', 25) }}
                                            </a>
                                        </div>
                                    </td>

                                    <td>
                                        @if ($category->parent)
                                            <a href="{{ route('backend.product-categories.show', ['productCategory' => $category->parent->uuid, 'slug' => $category->parent->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-muted">
                                                {{ Str::limit($category->parent->active_translation?->name ?? '—', 15) }}
                                            </a>
                                        @else
                                            <span class="badge badge-soft-secondary">Racine</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-categories.index') }}" class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-collection me-1"></i> {{ $category->children_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-categories.audits', ['productCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $category->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-categories.activity-logs', ['productCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-secondary">
                                                <i class="bx bx-pulse me-1"></i> {{ $category->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>{{ $category->sort_order }}</td>

                                    <td>
                                        <span class="badge {{ $category->status->badgeClass() }}">
                                            {{ $category->status->label() }}
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($category->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.product-categories.trashed'))
                                        <td class="text-muted">
                                            @formatDate($category->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if($category->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <form action="{{ route('backend.product-categories.restore', ['uuid' => $category->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $category->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('restore-form-{{ $category->uuid }}', 'Restaurer cette catégorie ?', 'Restaurer', 'success')" title="Restaurer">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-categories.force_destroy', ['uuid' => $category->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $category->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer définitivement" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $category->uuid }}', 'Supprimer définitivement cette catégorie ? Cette action est irréversible.')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$category->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.product-categories.show', ['productCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-light" title="Voir">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.product-categories.edit', ['productCategory' => $category->uuid]) }}" class="btn btn-sm btn-soft-primary" title="Modifier">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($category->status === \App\Enums\Products\Categories\ProductCategoryStatus::Active)
                                                    <form action="{{ route('backend.product-categories.deactivate', ['productCategory' => $category->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $category->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" onclick="confirmAction('deactivate-form-{{ $category->uuid }}', 'Désactiver cette catégorie ?', 'Désactiver', 'warning')" title="Désactiver">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.product-categories.activate', ['productCategory' => $category->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $category->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('activate-form-{{ $category->uuid }}', 'Activer cette catégorie ?', 'Activer', 'success')" title="Activer">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.product-categories.destroy', ['productCategory' => $category->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $category->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $category->uuid }}', 'Supprimer cette catégorie ?', 'Supprimer', 'warning')" title="Supprimer">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.product-categories.trashed') ? 12 : 11 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        Aucune catégorie trouvée.
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
            $('#product-categories-table').DataTable({
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

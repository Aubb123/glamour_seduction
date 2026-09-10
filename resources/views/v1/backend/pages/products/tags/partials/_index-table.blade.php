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
            'label' => 'Tags',
            'value' => $tags->count(),
            'icon'  => 'bx bx-purchase-tag',
            'color' => 'primary',
        ],
        [
            'label' => 'Actives',
            'value' => $tags->where('status', \App\Enums\Products\Tags\ProductTagStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'info',
        ],
        [
            'label' => 'Inactives',
            'value' => $tags->where('status', \App\Enums\Products\Tags\ProductTagStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => 'Audits',
            'value' => $tags->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => 'Activités',
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
                <h5 class="card-title mb-0">Tags de produits</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Créer une tag', 'icon' => 'bx bx-plus', 'route' => 'backend.product-tags.create'],
                                ['label' => 'Liste des tags', 'icon' => 'bx bx-list-ul', 'route' => 'backend.product-tags.index'],
                                ['label' => 'Corbeille', 'icon' => 'bx bx-trash', 'route' => 'backend.product-tags.trashed'],
                            ]
                        ])
                    </ul>
                    
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="product-tags-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Audits</th>
                                <th scope="col">Logs</th>
                                <th scope="col">Ordre</th>
                                <th scope="col">Actif</th>
                                <th scope="col">Créé le</th>
                                @if(request()->routeIs('backend.product-tags.trashed'))
                                    <th scope="col">Supprimé le</th>
                                @endif
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @forelse ($tags as $tag)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        <a href="{{ route('backend.product-tags.show', ['productTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-nowrap">
                                            {{ Str::limit($tag->active_translation?->name ?? '-', 20) }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-tags.audits', ['productTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $tag->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-tags.activity-logs', ['productTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-secondary">
                                                <i class="bx bx-pulse me-1"></i> {{ $tag->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>{{ $tag->sort_order }}</td>

                                    <td>
                                        <span class="badge {{ $tag->status->badgeClass() }}">
                                            {{ $tag->status->label() }}
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($tag->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.product-tags.trashed'))
                                        <td class="text-muted">
                                            @formatDate($tag->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if($tag->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <form action="{{ route('backend.product-tags.restore', ['uuid' => $tag->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $tag->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('restore-form-{{ $tag->uuid }}', 'Restaurer cette tag ?', 'Restaurer', 'success')" title="Restaurer">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-tags.force_destroy', ['uuid' => $tag->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $tag->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer définitivement" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $tag->uuid }}', 'Supprimer définitivement cette tag ? Cette action est irréversible.')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$tag->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.product-tags.show', ['productTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-light" title="Voir">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.product-tags.edit', ['productTag' => $tag->uuid]) }}" class="btn btn-sm btn-soft-primary" title="Modifier">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($tag->status === \App\Enums\Products\Tags\ProductTagStatus::Active)
                                                    <form action="{{ route('backend.product-tags.deactivate', ['productTag' => $tag->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $tag->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" onclick="confirmAction('deactivate-form-{{ $tag->uuid }}', 'Désactiver cette tag ?', 'Désactiver', 'warning')" title="Désactiver">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.product-tags.activate', ['productTag' => $tag->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $tag->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('activate-form-{{ $tag->uuid }}', 'Activer cette tag ?', 'Activer', 'success')" title="Activer">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.product-tags.destroy', ['productTag' => $tag->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $tag->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $tag->uuid }}', 'Supprimer cette tag ?', 'Supprimer', 'warning')" title="Supprimer">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.product-tags.trashed') ? 10 : 9 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        Aucune tag trouvée.
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
            $('#product-tags-table').DataTable({
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
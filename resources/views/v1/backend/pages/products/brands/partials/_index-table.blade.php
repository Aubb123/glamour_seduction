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
            'label' => 'Marques',
            'value' => $brands->count(),
            'icon'  => 'bx bx-purchase-tag',
            'color' => 'primary',
        ],
        [
            'label' => 'Actives',
            'value' => $brands->where('status', \App\Enums\Products\Brands\ProductBrandStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'info',
        ],
        [
            'label' => 'Inactives',
            'value' => $brands->where('status', \App\Enums\Products\Brands\ProductBrandStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => 'Audits',
            'value' => $brands->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => 'Activités',
            'value' => $brands->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Marques de produits</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Créer une marque', 'icon' => 'bx bx-plus', 'route' => 'backend.product-brands.create'],
                                ['label' => 'Liste des marques', 'icon' => 'bx bx-list-ul', 'route' => 'backend.product-brands.index'],
                                ['label' => 'Corbeille', 'icon' => 'bx bx-trash', 'route' => 'backend.product-brands.trashed'],
                            ]
                        ])
                    </ul>
                    
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="product-brands-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Logo</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Audits</th>
                                <th scope="col">Logs</th>
                                <th scope="col">Ordre</th>
                                <th scope="col">Actif</th>
                                <th scope="col">Créé le</th>
                                @if(request()->routeIs('backend.product-brands.trashed'))
                                    <th scope="col">Supprimé le</th>
                                @endif
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @forelse ($brands as $brand)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <a href="{{ $brand->getFirstImage()['url_img'] }}" target="_blank" rel="noopener noreferrer">
                                            <img src="{{ $brand->getFirstImage()['url_img'] }}" alt="{{ $brand->active_translation?->name ?? 'Logo marque' }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;">
                                        </a>
                                    </td>

                                    <td class="fw-bold">
                                        <a href="{{ route('backend.product-brands.show', ['productBrand' => $brand->uuid, 'slug' => $brand->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-nowrap">
                                            {{ Str::limit($brand->active_translation?->name ?? '-', 20) }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-brands.audits', ['productBrand' => $brand->uuid, 'slug' => $brand->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $brand->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-brands.activity-logs', ['productBrand' => $brand->uuid, 'slug' => $brand->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-secondary">
                                                <i class="bx bx-pulse me-1"></i> {{ $brand->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>{{ $brand->sort_order }}</td>

                                    <td>
                                        <span class="badge {{ $brand->status->badgeClass() }}">
                                            {{ $brand->status->label() }}
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($brand->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.product-brands.trashed'))
                                        <td class="text-muted">
                                            @formatDate($brand->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if($brand->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <form action="{{ route('backend.product-brands.restore', ['uuid' => $brand->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $brand->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('restore-form-{{ $brand->uuid }}', 'Restaurer cette marque ?', 'Restaurer', 'success')" title="Restaurer">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-brands.force_destroy', ['uuid' => $brand->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $brand->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer définitivement" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $brand->uuid }}', 'Supprimer définitivement cette marque ? Cette action est irréversible.')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$brand->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.product-brands.show', ['productBrand' => $brand->uuid, 'slug' => $brand->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-light" title="Voir">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.product-brands.edit', ['productBrand' => $brand->uuid]) }}" class="btn btn-sm btn-soft-primary" title="Modifier">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($brand->status === \App\Enums\Products\Brands\ProductBrandStatus::Active)
                                                    <form action="{{ route('backend.product-brands.deactivate', ['productBrand' => $brand->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $brand->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" onclick="confirmAction('deactivate-form-{{ $brand->uuid }}', 'Désactiver cette marque ?', 'Désactiver', 'warning')" title="Désactiver">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.product-brands.activate', ['productBrand' => $brand->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $brand->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('activate-form-{{ $brand->uuid }}', 'Activer cette marque ?', 'Activer', 'success')" title="Activer">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.product-brands.destroy', ['productBrand' => $brand->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $brand->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $brand->uuid }}', 'Supprimer cette marque ?', 'Supprimer', 'warning')" title="Supprimer">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.product-brands.trashed') ? 10 : 9 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        Aucune marque trouvée.
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
            $('#product-brands-table').DataTable({
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
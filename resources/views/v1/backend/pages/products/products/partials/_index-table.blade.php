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
            'label' => __('Produits'),
            'value' => $products->count(),
            'icon'  => 'bx bx-box',
            'color' => 'primary',
        ],
        [
            'label' => __('Publiés'),
            'value' => $products->where('status', \App\Enums\Products\Products\ProductStatus::Published)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Brouillons'),
            'value' => $products->where('status', \App\Enums\Products\Products\ProductStatus::Draft)->count(),
            'icon'  => 'bx bx-edit',
            'color' => 'warning',
        ],
        [
            'label' => __('Archivés'),
            'value' => $products->where('status', \App\Enums\Products\Products\ProductStatus::Archived)->count(),
            'icon'  => 'bx bx-archive',
            'color' => 'secondary',
        ],
        [
            'label' => __('Mis en avant'),
            'value' => $products->where('is_featured', true)->count(),
            'icon'  => 'bx bx-star',
            'color' => 'info',
        ],
        [
            'label' => __('Audits'),
            'value' => $products->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'danger',
        ],
        [
            'label' => __('Activités'),
            'value' => $products->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">{{ __('Liste des produits') }}</h5>

                @if (! request()->routeIs('backend.products.trashed'))
                    <a href="{{ route('backend.products.create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i> {{ __('Nouveau produit') }}
                    </a>
                @endif
            </div>

            <div class="card-body">

                {{-- Filtres --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted small">{{ __('Statut') }}</label>
                        <select id="filter-status" class="form-select form-select-sm">
                            <option value="">{{ __('Tous les statuts') }}</option>
                            @foreach (\App\Enums\Products\Products\ProductStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small">{{ __('Catégorie') }}</label>
                        <select id="filter-category" class="form-select form-select-sm">
                            <option value="">{{ __('Toutes les catégories') }}</option>
                            @foreach ($products->pluck('category')->filter()->unique('id') as $category)
                                <option value="{{ $category->id }}">{{ $category->active_translation?->name ?? ('#'.$category->id) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small">{{ __('Marque') }}</label>
                        <select id="filter-brand" class="form-select form-select-sm">
                            <option value="">{{ __('Toutes les marques') }}</option>
                            @foreach ($products->pluck('brand')->filter()->unique('id') as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->active_translation?->name ?? ('#'.$brand->id) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-soft-secondary w-100" id="btn-reset-filters">
                            <i class="bx bx-reset me-1"></i> {{ __('Réinitialiser') }}
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle w-100" id="products-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Nom') }}</th>
                                <th>{{ __('Catégorie') }}</th>
                                <th>{{ __('Marque') }}</th>
                                <th>{{ __('Statut') }}</th>
                                <th>{{ __('Prix') }}</th>
                                <th>{{ __('Audits') }}</th>
                                <th>{{ __('Logs') }}</th>
                                <th>{{ __('Mis en avant') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="fw-semibold" data-search-name>{{ $product->active_translation?->name ?? '—' }}</span>
                                        <small class="text-muted d-block"><code>{{ $product->uuid }}</code></small>
                                    </td>
                                    <td data-category="{{ $product->category_id }}">{{ $product->category?->active_translation?->name ?? '—' }}</td>
                                    <td data-brand="{{ $product->brand_id ?? 'null' }}">{{ $product->brand?->active_translation?->name ?? '—' }}</td>
                                    <td data-status="{{ $product->status->value }}">
                                        <span class="badge {{ $product->status->badgeClass() }} fs-14 px-3 py-2">
                                            {{ $product->status->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ number_format((float) $product->base_price, 2, ',', ' ') }}</span>
                                        @if ($product->compare_at_price)
                                            <del class="text-muted ms-1">{{ number_format((float) $product->compare_at_price, 2, ',', ' ') }}</del>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('backend.products.audits', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $product->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('backend.products.activity-logs', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-info">
                                                <i class="bx bx-pulse me-1"></i> {{ $product->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        @if ($product->is_featured)
                                            <span class="badge badge-soft-warning"><i class="bx bxs-star me-1"></i> {{ __('En avant') }}</span>
                                        @else
                                            <span class="badge badge-soft-secondary">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            @if (request()->routeIs('backend.products.trashed'))

                                                <form action="{{ route('backend.products.restore', ['uuid' => $product->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $product->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $product->uuid }}', '{{ __('Restaurer ce produit ?') }}', '{{ __('Restaurer') }}', 'success')">
                                                        <i class="bx bx-rotate-left"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.products.force_destroy', ['uuid' => $product->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $product->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $product->uuid }}', '{{ __('Supprimer définitivement ce produit ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            @else

                                                <a href="{{ route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-soft-secondary" title="{{ __('Détails') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.products.edit', ['product' => $product->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($product->status === \App\Enums\Products\Products\ProductStatus::Published)
                                                    <form action="{{ route('backend.products.unpublish', ['product' => $product->uuid]) }}" method="POST" class="d-inline" id="unpublish-form-{{ $product->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" title="{{ __('Dépublier') }}" onclick="confirmAction('unpublish-form-{{ $product->uuid }}', '{{ __('Dépublier ce produit ?') }}', '{{ __('Dépublier') }}', 'warning')">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @elseif ($product->status === \App\Enums\Products\Products\ProductStatus::Draft)
                                                    <form action="{{ route('backend.products.publish', ['product' => $product->uuid]) }}" method="POST" class="d-inline" id="publish-form-{{ $product->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Publier') }}" onclick="confirmAction('publish-form-{{ $product->uuid }}', '{{ __('Publier ce produit ?') }}', '{{ __('Publier') }}', 'success')">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.products.archive', ['product' => $product->uuid]) }}" method="POST" class="d-inline" id="archive-form-{{ $product->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-secondary" title="{{ __('Archiver') }}" onclick="confirmAction('archive-form-{{ $product->uuid }}', '{{ __('Archiver ce produit ?') }}', '{{ __('Archiver') }}', 'warning')">
                                                            <i class="bx bx-archive"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.products.destroy', ['product' => $product->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $product->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer') }}" onclick="confirmAction('delete-form-{{ $product->uuid }}', '{{ __('Supprimer ce produit ?') }}', '{{ __('Supprimer') }}', 'warning')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="bx bx-box fs-3 d-block mb-2"></i>
                                        {{ __('Aucun produit trouvé.') }}
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
        $(document).ready(function () {
            const table = $('#products-table').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>' +
                'tr'                                        +
                '<"row mt-3"<"col-md-5"l><"col-md-7"p>>',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: "{{ app()->getLocale() === 'fr' ? asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/fr-FR.json') : asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/en-GB.json') }}"
                },
                order: [[0, 'asc']],
                pageLength: 25,
            });

            // Filtres
            $('#filter-status').on('change', function () {
                table.column(4).search(this.value).draw();
            });
            $('#filter-category').on('change', function () {
                table.column(2).search(this.value).draw();
            });
            $('#filter-brand').on('change', function () {
                table.column(3).search(this.value).draw();
            });
            $('#btn-reset-filters').on('click', function () {
                $('#filter-status').val('').trigger('change');
                $('#filter-category').val('').trigger('change');
                $('#filter-brand').val('').trigger('change');
            });
        });
    </script>
@endpush
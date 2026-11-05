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

<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Liste des produits') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Nouveau produit', 'icon' => 'bx bx-plus', 'route' => 'backend.products.create'],
                                ['label' => 'Liste des produits', 'icon' => 'bx bx-list-ul', 'route' => 'backend.products.index'],
                                ['label' => 'Corbeille', 'icon' => 'bx bx-trash', 'route' => 'backend.products.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">

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
                                @php($defaultCurrency = \App\Models\Currency::where('is_default', true)->first())
                                @php($image = $product->getFirstImage())
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ $image['url_img'] }}" target="_blank" rel="noopener noreferrer">
                                                <img src="{{ $image['url_img'] }}" alt="{{ $product->active_translation?->name ?? __('Image produit') }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;">
                                            </a>
                                            <div>
                                                <span class="fw-semibold" data-search-name>{{ $product->active_translation?->name ?? '—' }}</span>
                                                <small class="text-muted d-block"><code>{{ $product->uuid }}</code></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-category="{{ $product->category_id }}">{{ $product->category?->active_translation?->name ?? '—' }}</td>
                                    <td data-brand="{{ $product->brand_id ?? 'null' }}">{{ $product->brand?->active_translation?->name ?? '—' }}</td>
                                    <td data-status="{{ $product->status->value }}">
                                        <span class="badge {{ $product->status->badgeClass() }} fs-14 px-3 py-2">
                                            {{ $product->status->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ number_format((float) $product->base_price, 2, ',', ' ') }}{{ $defaultCurrency?->symbol ? ' '.$defaultCurrency->symbol : '' }}</span>
                                        @if ($product->compare_at_price)
                                            <del class="text-muted ms-1">{{ number_format((float) $product->compare_at_price, 2, ',', ' ') }}{{ $defaultCurrency?->symbol ? ' '.$defaultCurrency->symbol : '' }}</del>
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
            $('#products-table').DataTable({
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
        });
    </script>
@endpush

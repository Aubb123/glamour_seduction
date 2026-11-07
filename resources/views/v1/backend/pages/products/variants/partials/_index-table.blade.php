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
            'label' => __('Variantes'),
            'value' => $variants->count(),
            'icon'  => 'bx bx-layer',
            'color' => 'primary',
        ],
        [
            'label' => __('Actives'),
            'value' => $variants->where('status', \App\Enums\Products\Variants\ProductVariantStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Inactives'),
            'value' => $variants->where('status', \App\Enums\Products\Variants\ProductVariantStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Audits'),
            'value' => $variants->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'info',
        ],
        [
            'label' => __("Logs d'activité"),
            'value' => $variants->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'warning',
        ],
    ],
])

<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">{{ __('Liste des variantes de produits') }}</h5>

                <div class="d-flex align-items-center gap-2">

                    {{-- Bouton de création : uniquement dans le contexte d'un produit précis
                         (l'inclusion depuis backend.products.show fournit $product) --}}
                    @isset($product)
                        <a href="{{ route('backend.product-variants.create', ['product' => $product->uuid]) }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i> {{ __('Nouvelle variante') }}
                        </a>
                    @endisset

                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('Actions') }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                            @include('v1.backend.layouts.partials._card_actions_menu', [
                                'actions' => [
                                    ['label' => __('Liste des variantes'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.product-variants.index'],
                                    ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.product-variants.trashed'],
                                ]
                            ])
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle w-100" id="product-variants-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>SKU</th>
                                <th>{{ __('Produit') }}</th>
                                <th>{{ __("Valeurs d'attributs") }}</th>
                                <th>{{ __('Prix') }}</th>
                                <th>{{ __('Statut') }}</th>
                                <th>{{ __('Par défaut') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($defaultCurrency = \App\Models\Currency::where('is_default', true)->first())
                            @forelse ($variants as $variant)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="fw-semibold"><code>{{ $variant->sku }}</code></span></td>
                                    <td>{{ $variant->product?->active_translation?->name ?? ('#'.$variant->product_id) }}</td>
                                    <td>
                                        @forelse ($variant->attributeValues as $value)
                                            <span class="badge badge-soft-primary me-1">{{ $value->active_translation?->label ?? $value->value }}</span>
                                        @empty
                                            <span class="text-muted">—</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @if ($variant->price)
                                            <span class="fw-bold">{{ number_format((float) $variant->price, 2, ',', ' ') }}{{ $defaultCurrency?->symbol ? ' '.$defaultCurrency->symbol : '' }}</span>
                                        @else
                                            <span class="text-muted" title="{{ __('Hérite du produit') }}">{{ number_format((float) ($variant->product?->base_price ?? 0), 2, ',', ' ') }}{{ $defaultCurrency?->symbol ? ' '.$defaultCurrency->symbol : '' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $variant->status->badgeClass() }} fs-14 px-3 py-2">
                                            {{ $variant->status->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($variant->is_default)
                                            <span class="badge badge-soft-success">{{ __('Par défaut') }}</span>
                                        @else
                                            <span class="badge badge-soft-secondary">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            @if (request()->routeIs('backend.product-variants.trashed'))

                                                <form action="{{ route('backend.product-variants.restore', ['uuid' => $variant->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $variant->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $variant->uuid }}', '{{ __('Restaurer cette variante ?') }}', '{{ __('Restaurer') }}', 'success')">
                                                        <i class="bx bx-rotate-left"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-variants.force_destroy', ['uuid' => $variant->uuid]) }}" method="POST" class="d-inline" id="force-delete-form-{{ $variant->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" onclick="confirmAction('force-delete-form-{{ $variant->uuid }}', '{{ __('Supprimer définitivement cette variante ?') }}', '{{ __('Supprimer définitivement') }}', 'danger')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            @else

                                                <a href="{{ route('backend.product-variants.show', ['productVariant' => $variant->uuid]) }}" class="btn btn-sm btn-soft-secondary" title="{{ __('Détails') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.product-variants.edit', ['productVariant' => $variant->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($variant->status === \App\Enums\Products\Variants\ProductVariantStatus::Active)
                                                    <form action="{{ route('backend.product-variants.deactivate', ['productVariant' => $variant->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $variant->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" title="{{ __('Désactiver') }}" onclick="confirmAction('deactivate-form-{{ $variant->uuid }}', '{{ __('Désactiver cette variante ?') }}', '{{ __('Désactiver') }}', 'warning')">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.product-variants.activate', ['productVariant' => $variant->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $variant->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Activer') }}" onclick="confirmAction('activate-form-{{ $variant->uuid }}', '{{ __('Activer cette variante ?') }}', '{{ __('Activer') }}', 'success')">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.product-variants.destroy', ['productVariant' => $variant->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $variant->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer') }}" onclick="confirmAction('delete-form-{{ $variant->uuid }}', '{{ __('Supprimer cette variante ?') }}', '{{ __('Supprimer') }}', 'warning')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bx bx-layer fs-3 d-block mb-2"></i>
                                        {{ __('Aucune variante trouvée.') }}
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
            $('#product-variants-table').DataTable({
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

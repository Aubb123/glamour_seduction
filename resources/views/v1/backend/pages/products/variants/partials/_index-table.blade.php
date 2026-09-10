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
            'label' => 'Variantes',
            'value' => $variants->count(),
            'icon'  => 'bx bx-layer',
            'color' => 'primary',
        ],
        [
            'label' => 'Actives',
            'value' => $variants->where('status', \App\Enums\Products\Variants\ProductVariantStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => 'Inactives',
            'value' => $variants->where('status', \App\Enums\Products\Variants\ProductVariantStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => 'Audits',
            'value' => $variants->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'info',
        ],
        [
            'label' => "Logs d'activité",
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
                <h5 class="card-title mb-0">Liste des variantes de produits</h5>

                @if (! request()->routeIs('backend.product-variants.trashed'))
                    <a href="{{ route('backend.product-variants.create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i> Nouvelle variante
                    </a>
                @endif
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle w-100" id="product-variants-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>SKU</th>
                                <th>Produit</th>
                                <th>Valeurs d'attributs</th>
                                <th>Prix</th>
                                <th>Statut</th>
                                <th>Par défaut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
<tbody>
                            @forelse ($variants as $variant)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="fw-semibold">{{ $variant->sku }}</span></td>
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
                                            <span class="fw-bold">{{ number_format((float) $variant->price, 2, ',', ' ') }}</span>
                                        @else
                                            <span class="text-muted" title="Hérite du produit">{{ number_format((float) ($variant->product?->base_price ?? 0), 2, ',', ' ') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $variant->status->badgeClass() }} fs-14 px-3 py-2">
                                            {{ $variant->status->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($variant->is_default)
                                            <span class="badge badge-soft-success">Par défaut</span>
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
                                                    <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('restore-form-{{ $variant->uuid }}', 'Restaurer cette variante ?', 'Restaurer', 'success')">
                                                        <i class="bx bx-rotate-left"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-variants.force-destroy', ['uuid' => $variant->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $variant->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $variant->uuid }}', 'Supprimer définitivement cette variante ?', 'Supprimer définitivement', 'danger')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            @else

                                                <a href="{{ route('backend.product-variants.show', ['productVariant' => $variant->uuid]) }}" class="btn btn-sm btn-soft-secondary" title="Détails">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.product-variants.edit', ['productVariant' => $variant->uuid]) }}" class="btn btn-sm btn-soft-primary" title="Modifier">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($variant->status === \App\Enums\Products\Variants\ProductVariantStatus::Active)
                                                    <form action="{{ route('backend.product-variants.deactivate', ['productVariant' => $variant->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $variant->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" title="Désactiver" onclick="confirmAction('deactivate-form-{{ $variant->uuid }}', 'Désactiver cette variante ?', 'Désactiver', 'warning')">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.product-variants.activate', ['productVariant' => $variant->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $variant->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" title="Activer" onclick="confirmAction('activate-form-{{ $variant->uuid }}', 'Activer cette variante ?', 'Activer', 'success')">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.product-variants.destroy', ['productVariant' => $variant->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $variant->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer" onclick="confirmAction('delete-form-{{ $variant->uuid }}', 'Supprimer cette variante ?', 'Supprimer', 'warning')">
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
                                        Aucune variante trouvée.
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
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#product-variants-table').DataTable({
                language: { url: '{{ asset(getEnvFolder() . 'others/backend/datatables/French.json') }}' },
                order: [[0, 'asc']],
                pageLength: 25,
            });
        });
    </script>
@endpush
                        <tbody>
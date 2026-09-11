<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Stocks par entrepôt / variante</h5>
                <a href="{{ route('backend.product-warehouse-stocks.create') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i> Nouveau stock
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="product-warehouse-stocks-table" class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Entrepôt</th>
                                <th>Produit</th>
                                <th>Variante (SKU)</th>
                                <th>Quantité</th>
                                <th>Réservée</th>
                                <th>Disponible</th>
                                <th>Seuil</th>
                                <th>État</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stocks as $stock)
                                @php
                                    $variant = $stock->variant;
                                    $product = $variant?->product;
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $stock->warehouse?->name }}</span>
                                        <small class="text-muted d-block">{{ $stock->warehouse?->code }}</small>
                                    </td>
                                    <td>{{ $product?->active_translation?->name ?? '—' }}</td>
                                    <td>{{ $variant?->sku ?? '—' }}</td>
                                    <td>{{ $stock->quantity }}</td>
                                    <td>{{ $stock->reserved_quantity }}</td>
                                    <td>{{ $stock->availableQuantity() }}</td>
                                    <td>{{ $stock->low_stock_threshold }}</td>
                                    <td>
                                        @if ($stock->quantity <= $stock->low_stock_threshold)
                                            <span class="badge badge-soft-danger">Stock bas</span>
                                        @else
                                            <span class="badge badge-soft-success">OK</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">

                                            <a href="{{ route('backend.product-warehouse-stocks.show', ['productWarehouseStock' => $stock->uuid]) }}" class="btn btn-sm btn-soft-primary" title="Voir">
                                                <i class="bx bx-show"></i>
                                            </a>

                                            <a href="{{ route('backend.product-warehouse-stocks.edit', ['productWarehouseStock' => $stock->uuid]) }}" class="btn btn-sm btn-soft-secondary" title="Modifier">
                                                <i class="bx bx-edit"></i>
                                            </a>

                                            <form action="{{ route('backend.product-warehouse-stocks.destroy', ['productWarehouseStock' => $stock->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $stock->uuid }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer" onclick="confirmAction('delete-form-{{ $stock->uuid }}', 'Supprimer ce stock ?', 'Supprimer', 'warning')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bx bx-package fs-3 d-block mb-2"></i>
                                        Aucun stock trouvé.
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
            $('#product-warehouse-stocks-table').DataTable({
                language: { url: '{{ asset(getEnvFolder() . 'others/backend/datatables/French.json') }}' },
                order: [[0, 'asc']],
                pageLength: 25,
            });
        });
    </script>
@endpush

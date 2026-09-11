<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Stocks supprimés</h5>
                <a href="{{ route('backend.product-warehouse-stocks.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> Retour
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="product-warehouse-stocks-trashed-table" class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Entrepôt</th>
                                <th>Variante (SKU)</th>
                                <th>Quantité</th>
                                <th>Réservée</th>
                                <th>Supprimé le</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stocks as $stock)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $stock->warehouse?->name }}</span>
                                        <small class="text-muted d-block">{{ $stock->warehouse?->code }}</small>
                                    </td>
                                    <td>{{ $stock->variant?->sku ?? '—' }}</td>
                                    <td>{{ $stock->quantity }}</td>
                                    <td>{{ $stock->reserved_quantity }}</td>
                                    <td>{{ $stock->deleted_at?->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">

                                            <form action="{{ route('backend.product-warehouse-stocks.restore', ['uuid' => $stock->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $stock->uuid }}">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-soft-success" title="Restaurer" onclick="confirmAction('restore-form-{{ $stock->uuid }}', 'Restaurer ce stock ?', 'Restaurer', 'info')">
                                                    <i class="bx bx-undo"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('backend.product-warehouse-stocks.force-destroy', ['uuid' => $stock->uuid]) }}" method="POST" class="d-inline" id="force-destroy-form-{{ $stock->uuid }}">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer définitivement" onclick="confirmAction('force-destroy-form-{{ $stock->uuid }}', 'Supprimer définitivement ce stock ?', 'Supprimer définitivement', 'danger')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bx bx-trash fs-3 d-block mb-2"></i>
                                        Aucun stock supprimé.
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
            $('#product-warehouse-stocks-trashed-table').DataTable({
                language: { url: '{{ asset(getEnvFolder() . 'others/backend/datatables/French.json') }}' },
                order: [[4, 'desc']],
                pageLength: 25,
            });
        });
    </script>
@endpush

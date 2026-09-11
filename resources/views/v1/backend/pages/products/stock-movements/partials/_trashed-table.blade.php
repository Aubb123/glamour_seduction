<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Mouvements de stock supprimés</h5>
                <a href="{{ route('backend.product-stock-movements.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> Retour
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="product-stock-movements-trashed-table" class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Entrepôt</th>
                                <th>Variante (SKU)</th>
                                <th>Type</th>
                                <th>Quantité</th>
                                <th>Supprimé le</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($movements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>{{ $movement->warehouse?->name }}</td>
                                    <td>{{ $movement->variant?->sku ?? '—' }}</td>
                                    <td><span class="badge {{ $movement->type->badgeClass() }}">{{ $movement->type->label() }}</span></td>
                                    <td>{{ $movement->quantity }}</td>
                                    <td>{{ $movement->deleted_at?->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">

                                            <form action="{{ route('backend.product-stock-movements.restore', ['uuid' => $movement->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $movement->uuid }}">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-soft-success" title="Restaurer" onclick="confirmAction('restore-form-{{ $movement->uuid }}', 'Restaurer ce mouvement ?', 'Restaurer', 'info')">
                                                    <i class="bx bx-undo"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('backend.product-stock-movements.force-destroy', ['uuid' => $movement->uuid]) }}" method="POST" class="d-inline" id="force-destroy-form-{{ $movement->uuid }}">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer définitivement" onclick="confirmAction('force-destroy-form-{{ $movement->uuid }}', 'Supprimer définitivement ce mouvement ?', 'Supprimer définitivement', 'danger')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bx bx-trash fs-3 d-block mb-2"></i>
                                        Aucun mouvement supprimé.
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
            $('#product-stock-movements-trashed-table').DataTable({
                language: { url: '{{ asset(getEnvFolder() . 'others/backend/datatables/French.json') }}' },
                order: [[5, 'desc']],
                pageLength: 25,
            });
        });
    </script>
@endpush

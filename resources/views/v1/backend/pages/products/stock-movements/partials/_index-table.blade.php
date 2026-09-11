<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Mouvements de stock (audit & traçabilité)</h5>
                <a href="{{ route('backend.product-stock-movements.create') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i> Nouveau mouvement
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="product-stock-movements-table" class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Entrepôt</th>
                                <th>Variante (SKU)</th>
                                <th>Type</th>
                                <th>Quantité</th>
                                <th>Note</th>
                                <th>Par</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($movements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $movement->warehouse?->name }}</span>
                                        <small class="text-muted d-block">{{ $movement->warehouse?->code }}</small>
                                    </td>
                                    <td>{{ $movement->variant?->sku ?? '—' }}</td>
                                    <td><span class="badge {{ $movement->type->badgeClass() }}">{{ $movement->type->label() }}</span></td>
                                    <td>
                                        @if ($movement->type->decreasesStock())
                                            <span class="text-danger fw-semibold">−{{ $movement->quantity }}</span>
                                        @else
                                            <span class="text-success fw-semibold">+{{ $movement->quantity }}</span>
                                        @endif
                                    </td>
                                    <td class="text-truncate" style="max-width: 220px;" title="{{ $movement->note }}">{{ $movement->note ?? '—' }}</td>
                                    <td>@php $reporter = $movement->createdBy; @endphp
{{ $reporter ? $reporter->first_name.' '.$reporter->last_name : '—' }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">

                                            <a href="{{ route('backend.product-stock-movements.show', ['productStockMovement' => $movement->uuid]) }}" class="btn btn-sm btn-soft-primary" title="Voir">
                                                <i class="bx bx-show"></i>
                                            </a>


                                            <form action="{{ route('backend.product-stock-movements.destroy', ['productStockMovement' => $movement->uuid]) }}" method="POST" class="d-inline" id="destroy-form-{{ $movement->uuid }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer" onclick="confirmAction('destroy-form-{{ $movement->uuid }}', 'Supprimer ce mouvement ?', 'Supprimer', 'warning')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bx bx-transfer fs-3 d-block mb-2"></i>
                                        Aucun mouvement de stock trouvé.
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
            $('#product-stock-movements-table').DataTable({
                language: { url: '{{ asset(getEnvFolder() . 'others/backend/datatables/French.json') }}' },
                order: [[0, 'desc']],
                pageLength: 25,
            });
        });
    </script>
@endpush

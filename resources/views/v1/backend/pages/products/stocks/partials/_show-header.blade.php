<div class="row">
    <div class="col-sm-12">
        <div class="card mb-3">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">

                <div>
                    <h5 class="mb-1">
                        {{ $stock->warehouse?->name ?? 'Entrepôt inconnu' }}
                        <span class="text-muted">/</span>
                        {{ $stock->variant?->sku ?? 'Variante inconnue' }}
                    </h5>
                    <div class="d-flex gap-2 flex-wrap">
                        @if ($stock->quantity <= $stock->low_stock_threshold)
                            <span class="badge badge-soft-danger">Stock bas</span>
                        @else
                            <span class="badge badge-soft-success">OK</span>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-1">

                    <a href="{{ route('backend.product-warehouse-stocks.audits', ['productWarehouseStock' => $stock->uuid]) }}" class="btn btn-sm btn-soft-info" title="Audits">
                        <i class="bx bx-history me-1"></i> Audits
                        <span class="badge ms-1">{{ $stock->audits_count ?? $stock->audits->count() }}</span>
                    </a>

                    <a href="{{ route('backend.product-warehouse-stocks.activity-logs', ['productWarehouseStock' => $stock->uuid]) }}" class="btn btn-sm btn-soft-secondary" title="Journaux d'activité">
                        <i class="bx bx-list-ul me-1"></i> Journaux
                        <span class="badge ms-1">{{ $stock->activity_logs_count ?? $stock->activityLogs->count() }}</span>
                    </a>

                    <a href="{{ route('backend.product-warehouse-stocks.edit', ['productWarehouseStock' => $stock->uuid]) }}" class="btn btn-sm btn-soft-primary" title="Modifier">
                        <i class="bx bx-edit me-1"></i> Modifier
                    </a>

                    <form action="{{ route('backend.product-warehouse-stocks.destroy', ['productWarehouseStock' => $stock->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $stock->uuid }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer" onclick="confirmAction('delete-form-{{ $stock->uuid }}', 'Supprimer ce stock ?', 'Supprimer', 'warning')">
                            <i class="bx bx-trash me-1"></i> Supprimer
                        </button>
                    </form>

                    <a href="{{ route('backend.product-warehouse-stocks.index') }}" class="btn btn-sm btn-soft-secondary" title="Retour">
                        <i class="bx bx-arrow-back"></i>
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>

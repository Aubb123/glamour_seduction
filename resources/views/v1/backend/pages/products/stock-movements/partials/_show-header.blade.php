<div class="row">
    <div class="col-sm-12">
        <div class="card mb-3">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">

                <div>
                    <h5 class="mb-1">Mouvement — {{ $movement->type->label() }} de {{ $movement->quantity }} unité(s)</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge {{ $movement->type->badgeClass() }}">{{ $movement->type->label() }}</span>
                        <small class="text-muted">{{ $movement->warehouse?->name }} / {{ $movement->variant?->sku }}</small>
                    </div>
                </div>

                <div class="d-flex gap-1">

                    <a href="{{ route('backend.product-stock-movements.index') }}" class="btn btn-sm btn-soft-secondary" title="Retour">
                        <i class="bx bx-arrow-back"></i>
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>

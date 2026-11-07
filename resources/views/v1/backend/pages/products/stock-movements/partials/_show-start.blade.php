<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    {{ __('Mouvement de stock') }} — {{ $movement->type->label() }} {{ $movement->quantity }}
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.product-stock-movements.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog me-1"></i> {{ __('Actions') }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                            <li>
                                <form action="{{ route('backend.product-stock-movements.destroy', ['productStockMovement' => $movement->uuid]) }}" method="POST" id="delete-form-{{ $movement->uuid }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $movement->uuid }}', '{{ __('Supprimer ce mouvement ?') }}', '{{ __('Supprimer') }}', 'warning')">
                                        <i class="bx bx-trash me-2 text-danger"></i> {{ __('Supprimer') }}
                                    </button>
                                </form>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a href="{{ route('backend.product-stock-movements.trashed') }}" class="dropdown-item text-warning">
                                    <i class="bx bx-trash me-2"></i> {{ __('Corbeille') }}
                                </a>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>

            <div class="card-body">

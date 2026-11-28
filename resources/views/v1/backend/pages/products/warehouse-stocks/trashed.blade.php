@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Stocks en corbeille') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Stocks en corbeille')])

    <div class="row">
        <div class="col-xl-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Stocks en corbeille') }}</h5>
                    <a href="{{ route('backend.product-warehouse-stocks.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered text-nowrap" id="product-warehouse-stocks-trashed-table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ __('Variante') }}</th>
                                    <th scope="col">{{ __('Entrepôt') }}</th>
                                    <th scope="col">{{ __('Quantité') }}</th>
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                    <th scope="col">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productWarehouseStocks as $productWarehouseStock)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-bold">{{ $productWarehouseStock->variant?->sku ?? '—' }}</td>
                                        <td>{{ $productWarehouseStock->warehouse?->active_translation?->name ?? $productWarehouseStock->warehouse?->code ?? '—' }}</td>
                                        <td><span class="badge badge-soft-secondary">{{ number_format((int) $productWarehouseStock->quantity, 0, '.', ' ') }}</span></td>
                                        <td class="text-muted">@formatDate($productWarehouseStock->deleted_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                        <td>
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.product-warehouse-stocks.restore', ['uuid' => $productWarehouseStock->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $productWarehouseStock->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $productWarehouseStock->uuid }}', '{{ __('Restaurer ce stock ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-warehouse-stocks.force_destroy', ['uuid' => $productWarehouseStock->uuid]) }}" method="POST" class="d-inline" id="force-delete-form-{{ $productWarehouseStock->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" onclick="confirmAction('force-delete-form-{{ $productWarehouseStock->uuid }}', '{{ __('Supprimer définitivement ce stock ?') }}', 'Supprimer', 'warning')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                            {{ __('Aucun stock en corbeille.') }}
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

@endsection
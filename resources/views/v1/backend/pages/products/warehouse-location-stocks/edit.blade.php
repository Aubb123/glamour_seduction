@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Modifier un stock par emplacement') }} — <code>{{ $productWarehouseLocationStock->variant?->sku ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Modifier un stock par emplacement')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('Modifier') }} — <code>{{ $productWarehouseLocationStock->variant?->sku ?? '' }}</code>
                    </h5>
                    <a href="{{ route('backend.product-warehouse-location-stocks.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Emplacement') }}</label>
                            <input type="text" class="form-control" value="{{ $productWarehouseLocationStock->location?->code ?? '—' }}" readonly disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Variante') }}</label>
                            <input type="text" class="form-control" value="{{ $productWarehouseLocationStock->variant?->sku ?? '—' }}" readonly disabled />
                        </div>
                    </div>

                    <form id="edit-location-stock-form"
                          action="{{ route('backend.product-warehouse-location-stocks.update', ['productWarehouseLocationStock' => $productWarehouseLocationStock->uuid]) }}"
                          method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">{{ __('Quantité') }} <span class="text-danger">*</span></label>
                                <input type="number" step="1" min="0" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $productWarehouseLocationStock->quantity) }}" required />
                                @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">{{ __('La quantité ne peut pas être négative.') }}</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save me-1"></i> {{ __('Mettre à jour') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
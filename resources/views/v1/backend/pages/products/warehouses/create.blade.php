@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Créer un entrepôt') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Créer un entrepôt')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Créer un entrepôt') }}</h5>
                    <a href="{{ route('backend.product-warehouses.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">
                    @include('v1.backend.pages.products.warehouses.partials._form', [
                        'langs' => $langs,
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection

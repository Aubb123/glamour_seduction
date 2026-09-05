@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Paramètres') }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('Paramètres') }} — {{ __('Audits') }}
                    </h5>
                </div>
                <div class="card-body">
                    @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])
                </div>
            </div>
        </div>
    </div>

@endsection
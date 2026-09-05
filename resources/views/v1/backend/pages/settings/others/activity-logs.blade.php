@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Paramètres') }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('Paramètres') }} — {{ __('Journal d\'activité') }}
                    </h5>
                </div>
                <div class="card-body">
                    @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])
                </div>
            </div>
        </div>
    </div>

@endsection
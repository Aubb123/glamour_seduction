@extends('v1.backend.layouts.app')

@section('title') {{ __('Historique des abonnements') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Historique des abonnements')])

    @include('v1.backend.pages.subscribes.histories.partials._index-table', [
        'histories' => $histories
    ])

@endsection
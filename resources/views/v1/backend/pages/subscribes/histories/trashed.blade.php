@extends('v1.backend.layouts.app')

@section('title') {{ __('Corbeille de l\'historique') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille de l\'historique')])

    @include('v1.backend.pages.subscribes.histories.partials._index-table', [
        'histories' => $histories
    ])

@endsection
@extends('v1.backend.layouts.app')

@section('title') {{ __('Abonnés') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Abonnés')])

    @include('v1.backend.pages.subscribes.partials._index-table', [
        'subscribes' => $subscribes
    ])

@endsection
@extends('v1.backend.layouts.app')

@section('title') {{ __('Corbeille des plateformes cloud') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille des plateformes cloud')])

    @include('v1.backend.pages.clouds.platforms.partials._index-table', [
        'platforms' => $platforms
    ])

@endsection
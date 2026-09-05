@extends('v1.backend.layouts.app')

@section('title') {{ __('Plateformes Cloud') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Plateformes Cloud')])

    @include('v1.backend.pages.clouds.platforms.partials._index-table', [
        'platforms' => $platforms
    ])

@endsection
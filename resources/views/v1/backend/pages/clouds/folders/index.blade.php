@extends('v1.backend.layouts.app')

@section('title') {{ __('Dossiers Cloud') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Dossiers Cloud')])

    @include('v1.backend.pages.clouds.folders.partials._index-table', [
        'folders' => $folders
    ])

@endsection
@extends('v1.backend.layouts.app')

@section('title') {{ __('Synchronisation Cloud') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Synchronisation Cloud')])

    @include('v1.backend.pages.clouds.files.partials._sync-table', [
        'files' => $files
    ])

@endsection
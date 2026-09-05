@extends('v1.backend.layouts.app')

@section('title') {{ __('Fichiers Cloud') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Fichiers Cloud')])

    @include('v1.backend.pages.clouds.files.partials._index-table', [
        'files' => $files
    ])

@endsection
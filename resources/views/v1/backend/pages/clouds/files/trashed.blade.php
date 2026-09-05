@extends('v1.backend.layouts.app')

@section('title') Fichiers - Corbeille @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Fichiers - Corbeille'])

    @include('v1.backend.pages.clouds.files.partials._index-table', [
        'files' => $files
    ])

@endsection
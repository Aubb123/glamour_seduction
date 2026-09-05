@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du dossier') }} — {{ $folder->folder_name }} - {{ __('Fichiers') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Fichiers du dossier')])

    @include('v1.backend.pages.clouds.folders.partials._show-start', ['folder' => $folder])

        @include('v1.backend.pages.clouds.files.partials._index-table', ['files' => $files])

    @include('v1.backend.pages.clouds.folders.partials._show-end', ['folder' => $folder])

@endsection
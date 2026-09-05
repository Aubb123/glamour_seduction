@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du dossier') }} — {{ $folder->folder_name }} - {{ __('Sous-dossiers') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Sous-dossiers')])

    @include('v1.backend.pages.clouds.folders.partials._show-start', ['folder' => $folder])

        @include('v1.backend.pages.clouds.folders.partials._index-table', ['folders' => $subFolders])

    @include('v1.backend.pages.clouds.folders.partials._show-end', ['folder' => $folder])

@endsection
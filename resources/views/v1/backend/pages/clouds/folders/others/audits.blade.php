@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du dossier') }} — {{ $folder->folder_name }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.clouds.folders.partials._show-start', ['folder' => $folder])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.clouds.folders.partials._show-end', ['folder' => $folder])

@endsection
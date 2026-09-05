@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du dossier') }} — {{ $folder->folder_name }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.clouds.folders.partials._show-start', ['folder' => $folder])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.clouds.folders.partials._show-end', ['folder' => $folder])

@endsection
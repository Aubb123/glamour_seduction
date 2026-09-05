@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du fichier') }} — {{ $file->file_name }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.clouds.files.partials._show-start', ['file' => $file])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.clouds.files.partials._show-end', ['file' => $file])

@endsection
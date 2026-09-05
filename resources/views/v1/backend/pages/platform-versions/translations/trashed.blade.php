@extends('v1.backend.layouts.app') 

@section('title') Platform Versions Translations - Trashed @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Platform Versions Translations - Trashed'])

    @include('v1.backend.pages.platform-versions.partials._trashed-table-translations', [
        'platformVersionTranslations' => $platformVersionTranslations,
        'version' => $version,
    ])

@endsection
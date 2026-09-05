@extends('v1.backend.layouts.app')

@section('title') Tags - Traductions supprimées @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Tags - Traductions supprimées'])

    @include('v1.backend.pages.blogs.tags.partials._trashed-table-translations', [
        'blogTagTranslations' => $trashedTranslations,
        'tag' => $tag
    ])

@endsection
@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Traductions supprimées') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Traductions supprimées')])

    @include('v1.backend.pages.blogs.categories.partials._trashed-table-translations', [
        'blogCategoryTranslations' => $trashedTranslations,
        'category' => $category
    ])

@endsection
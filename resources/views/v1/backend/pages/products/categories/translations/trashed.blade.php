@extends('v1.backend.layouts.app')

@section('title')
    Traductions supprimées
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Traductions supprimées'])

    @include('v1.backend.pages.products.categories.partials._trashed-table-translations', [
        'productCategoryTranslations' => $trashedTranslations,
        'category' => $category
    ])

@endsection

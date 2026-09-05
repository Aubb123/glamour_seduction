@extends('v1.backend.layouts.app')

@section('title') Catégories - Corbeille @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Catégories - Corbeille'])

    @include('v1.backend.pages.blogs.categories.partials._index-table', [
        'categories' => $categories
    ])

@endsection
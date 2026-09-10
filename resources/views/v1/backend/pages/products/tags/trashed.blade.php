@extends('v1.backend.layouts.app')

@section('title') Tags de produits - Corbeille @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Tags de produits - Corbeille'])

    @include('v1.backend.pages.products.tags.partials._index-table', [
        'tags' => $tags
    ])

@endsection
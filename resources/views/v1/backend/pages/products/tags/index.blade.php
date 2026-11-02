@extends('v1.backend.layouts.app') 

@section('title')
    {{ __('Tags de produits') }}
@endsection

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Tags de produits'])

    @include('v1.backend.pages.products.tags.partials._index-table', [
        'tags' => $tags
    ])

@endsection
@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du tag') }} — {{ $tag->active_translation?->name ?? '' }} - {{ __('Produits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Produits')])

    @include('v1.backend.pages.products.tags.partials._show-start', ['tag' => $tag])

        @include('v1.backend.pages.products.products.partials._index-table', [
            'products' => $products,
        ])

    @include('v1.backend.pages.products.tags.partials._show-end', ['tag' => $tag])

@endsection

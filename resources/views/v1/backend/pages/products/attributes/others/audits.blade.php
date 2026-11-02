@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'attribut') }} — {{ $attribute->active_translation?->name ?? '' }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.products.attributes.partials._show-start', ['attribute' => $attribute])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.products.attributes.partials._show-end', ['attribute' => $attribute])

@endsection

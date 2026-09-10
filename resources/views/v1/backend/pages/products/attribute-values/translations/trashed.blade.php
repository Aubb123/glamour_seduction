@extends('v1.backend.layouts.app')

@section('title')
    Traductions supprimées
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Traductions supprimées'])

    @include('v1.backend.pages.products.attribute-values.partials._trashed-table-translations', [
        'productAttributeValueTranslations' => $trashedTranslations,
        'value' => $value
    ])

@endsection
@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Corbeille des traductions') }} — <code>{{ $value->active_translation?->label ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille des traductions')])

    @include('v1.backend.pages.products.attribute-values.partials._trashed-table-translations', [
        'productAttributeValueTranslations' => $trashedTranslations,
        'value' => $value
    ])

@endsection
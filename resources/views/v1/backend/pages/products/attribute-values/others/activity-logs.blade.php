@extends('v1.backend.layouts.app')

@section('title')
    Détail de la valeur d'attribut — {{ $value->active_translation?->label ?? '' }} - Journal d'activité
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Journal d'activité"])

    @include('v1.backend.pages.products.attribute-values.partials._show-start', ['value' => $value])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.products.attribute-values.partials._show-end', ['value' => $value])

@endsection
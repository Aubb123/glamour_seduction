@extends('v1.backend.layouts.app')

@section('title') Détail du poste — {{ $position->active_translation?->name ?? '' }} - Audits @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Audits'])

    @include('v1.backend.pages.shops.positions.partials._show-start', ['position' => $position])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.shops.positions.partials._show-end', ['position' => $position])

@endsection

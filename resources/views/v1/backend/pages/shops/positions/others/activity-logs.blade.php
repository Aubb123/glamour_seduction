@extends('v1.backend.layouts.app')

@section('title') Détail du poste — {{ $position->active_translation?->name ?? '' }} - Journal d'activité @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Journal d'activité"])

    @include('v1.backend.pages.shops.positions.partials._show-start', ['position' => $position])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.shops.positions.partials._show-end', ['position' => $position])

@endsection

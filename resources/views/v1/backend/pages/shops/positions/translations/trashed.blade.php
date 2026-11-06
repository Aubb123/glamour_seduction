@extends('v1.backend.layouts.app')

@section('title') {{ __('Corbeille des traductions') }} — <code>{{ $position->active_translation?->name ?? '' }}</code> @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille des traductions')])

    @include('v1.backend.pages.shops.positions.partials._trashed-table-translations', [
        'positionTranslations' => $trashedTranslations,
        'position' => $position
    ])

@endsection

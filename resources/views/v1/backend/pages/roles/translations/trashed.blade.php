@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Corbeille des traductions') }} — <code>{{ $role->active_translation?->label ?? $role->slug ?? '—' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille des traductions')])

    @include('v1.backend.pages.roles.partials._trashed-table-translations', [
        'roleTranslations' => $trashedTranslations,
        'role' => $role,
    ])

@endsection
@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/countries/others/users.pageTitle') }} — {{ $country->active_translation?->name ?? $country->code }} - {{ __('v1/backend/views/pages/geos/countries/others/users.utilisateurs') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/countries/others/users.utilisateursDuPays')])

    @include('v1.backend.pages.geos.countries.partials._show-start', ['country' => $country])

        @include('v1.backend.pages.users.partials._index-table', ['users' => $users])

    @include('v1.backend.pages.geos.countries.partials._show-end', ['country' => $country])

@endsection
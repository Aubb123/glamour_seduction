@extends('v1.backend.layouts.app')

@section('title') {{ __('v1/backend/views/pages/faqs/index.pageTitle') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/faqs/index.pageTitle')])

    @include('v1.backend.pages.faqs.partials._index-table', [
        'faqs' => $faqs
    ])

@endsection
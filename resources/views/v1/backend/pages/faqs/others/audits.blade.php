@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/faqs/others/audits.detailFaq') }} — {{ $faq->active_translation?->question ?? '' }} - {{ __('v1/backend/views/pages/faqs/others/audits.pageTitle') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/faqs/others/audits.pageTitle')])

    @include('v1.backend.pages.faqs.partials._show-start', ['faq' => $faq])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.faqs.partials._show-end', ['faq' => $faq])

@endsection
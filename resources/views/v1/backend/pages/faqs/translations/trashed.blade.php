@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/faqs/translations/trashed.pageTitle') }} — <code>{{ $faq->active_translation?->question ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/faqs/translations/trashed.pageTitle')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('v1/backend/views/pages/faqs/translations/trashed.pageTitle') }} — <code>{{ $faq->active_translation?->question ?? '' }}</code>
                    </h5>
                    <a href="{{ route('backend.faqs.show', ['faq' => $faq->uuid, 'slug' => $faq->active_translation?->slug ?? 'null']) }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/faqs/translations/trashed.retour') }}
                    </a>
                </div>

                <div class="card-body">
                    @if ($faqTranslations->isEmpty())
                        <p class="text-muted mb-0 text-center py-3">
                            <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                            {{ __('v1/backend/views/pages/faqs/translations/trashed.aucuneTraductionSupprimee') }}
                        </p>
                    @else
                        @include('v1.backend.pages.faqs.partials._trashed-table-translations', [
                            'faqTranslations' => $faqTranslations,
                            'faq' => $faq
                        ])
                    @endif
                </div>

            </div>
        </div>
    </div>

@endsection
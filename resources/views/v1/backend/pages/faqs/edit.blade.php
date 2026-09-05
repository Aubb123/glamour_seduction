@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/faqs/edit.pageTitle') }} — <code>{{ $faq->active_translation?->question ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/faqs/edit.pageTitle')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('v1/backend/views/pages/faqs/edit.modifier') }} — <code>{{ $faq->active_translation?->question ?? '' }}</code>
                    </h5>
                    <a href="{{ route('backend.faqs.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/faqs/edit.retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <form id="edit-faq-form"
                          action="{{ route('backend.faqs.update', ['faq' => $faq->uuid]) }}"
                          method="POST">
                        @csrf
                        @method('PUT')

                        <ul class="nav nav-tabs mb-3">
                            @foreach ($langs as $index => $lang)
                                <li class="nav-item">
                                    <a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#tab_lang_{{ $lang->code }}">
                                        {{ $lang->name }}
                                        @error('translations.' . $lang->code . '.question')
                                            <span class="text-danger ms-1">•</span>
                                        @enderror
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach ($langs as $index => $lang)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="tab_lang_{{ $lang->code }}">

                                    <input type="hidden" name="translations[{{ $lang->code }}][lang_id]" value="{{ $lang->id }}" />

                                    <div class="mb-3">
                                        <label for="question_{{ $lang->code }}" class="form-label">{{ __('v1/backend/views/pages/faqs/edit.question') }} <span class="text-danger">*</span> <span class="text-muted">({{ $lang->name }})</span></label>
                                        <input type="text" name="translations[{{ $lang->code }}][question]" id="question_{{ $lang->code }}" required class="form-control @error('translations.' . $lang->code . '.question') is-invalid @enderror" value="{{ old('translations.' . $lang->code . '.question', getTranslation($faq, $lang->id)?->question) }}" />
                                        @error('translations.' . $lang->code . '.question') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="answer_{{ $lang->code }}" class="form-label">{{ __('v1/backend/views/pages/faqs/edit.reponse') }} <span class="text-danger">*</span> <span class="text-muted">({{ $lang->name }})</span></label>
                                        <textarea name="translations[{{ $lang->code }}][answer]" id="answer_{{ $lang->code }}" required class="form-control @error('translations.' . $lang->code . '.answer') is-invalid @enderror" rows="6">{{ old('translations.' . $lang->code . '.answer', getTranslation($faq, $lang->id)?->answer) }}</textarea>
                                        @error('translations.' . $lang->code . '.answer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sort_order" class="form-label">{{ __('v1/backend/views/pages/faqs/edit.ordreAffichage') }} <span class="text-danger">*</span></label>
                                <input type="number" name="sort_order" id="sort_order" required class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $faq->sort_order) }}" min="0" required />
                                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ __('Statut') }}</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $faq->status?->value ?? 'inactive') === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                    <option value="inactive" {{ old('status', $faq->status?->value ?? 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save me-1"></i> {{ __('v1/backend/views/pages/faqs/edit.mettreAJour') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
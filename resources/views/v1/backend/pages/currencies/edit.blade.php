@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/currencies/edit.pageTitle') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/currencies/edit.pageTitle')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('v1/backend/views/pages/currencies/edit.modifier') }} — <span class="text-muted">{{ $currency->code }}</span>
                    </h5>
                    <a href="{{ route('backend.currencies.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/currencies/edit.retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <div class="d-flex align-items-center mb-4 gap-3">
                        <div class="step-item" data-step="1">
                            <span class="step-badge">1</span>
                            <span class="step-label">{{ __('v1/backend/views/pages/currencies/edit.informationsGenerales') }}</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-item" data-step="2">
                            <span class="step-badge">2</span>
                            <span class="step-label">{{ __('v1/backend/views/pages/currencies/edit.traductions') }}</span>
                        </div>
                    </div>

                    <form id="edit-currency-form" action="{{ route('backend.currencies.update', ['currency' => $currency->uuid]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="step-content" data-step-content="1">

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="code" class="form-label">{{ __('v1/backend/views/pages/currencies/edit.codeDevise') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" required class="form-control @error('code') is-invalid @enderror" placeholder="ex: EUR, USD, XOF..." value="{{ old('code', $currency->code) }}" maxlength="3" />
                                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/currencies/edit.troisLettresMajuscules') }}</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="symbol" class="form-label">{{ __('v1/backend/views/pages/currencies/edit.symboleMonetaire') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="symbol" id="symbol" required class="form-control @error('symbol') is-invalid @enderror" placeholder="{{ __('v1/backend/views/pages/currencies/edit.symboleExemple') }}" value="{{ old('symbol', $currency->symbol) }}" maxlength="10" />
                                    @error('symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="numeric_code" class="form-label">{{ __('v1/backend/views/pages/currencies/edit.codeNumerique') }}</label>
                                    <input type="text" name="numeric_code" id="numeric_code" class="form-control @error('numeric_code') is-invalid @enderror" placeholder="ex: 978, 840..." value="{{ old('numeric_code', $currency->numeric_code) }}" maxlength="3" />
                                    @error('numeric_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/currencies/edit.troisChiffresOptionnel') }}</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="decimal_places" class="form-label">{{ __('v1/backend/views/pages/currencies/edit.decimales') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="decimal_places" id="decimal_places" required class="form-control @error('decimal_places') is-invalid @enderror" value="{{ old('decimal_places', $currency->decimal_places) }}" min="0" max="4" />
                                    @error('decimal_places') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/currencies/edit.decimalesAide') }}</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="sort_order" class="form-label">{{ __('v1/backend/views/pages/currencies/edit.ordreAffichage') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="sort_order" id="sort_order" required class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $currency->sort_order) }}" min="0" />
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/currencies/edit.utiliseDansListesSelects') }}</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="status" class="form-label">{{ __('v1/backend/views/pages/currencies/edit.statut') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" required class="form-select @error('status') is-invalid @enderror">
                                        <option value="active" {{ old('status', $currency->status?->value ?? 'inactive') === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                        <option value="inactive" {{ old('status', $currency->status?->value ?? 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="exchange_rate" class="form-label">{{ __('v1/backend/views/pages/currencies/edit.tauxDeChange') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="exchange_rate" id="exchange_rate" step="0.000001" min="0.000001" required class="form-control @error('exchange_rate') is-invalid @enderror" placeholder="ex : 655.957000" value="{{ old('exchange_rate', $currency->exchange_rate) }}" />
                                    @error('exchange_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/currencies/edit.tauxDeChangeAide') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_default" value="0" />
                                        <input type="checkbox" name="is_default" id="is_default" value="1" class="form-check-input @error('is_default') is-invalid @enderror" {{ old('is_default', $currency->is_default) ? 'checked' : '' }} />
                                        <label for="is_default" class="form-check-label">{{ __('v1/backend/views/pages/currencies/edit.deviseParDefaut') }}</label>
                                        @error('is_default') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <small class="text-muted">{{ __('v1/backend/views/pages/currencies/edit.deviseParDefautAide') }}</small>
                                </div>
                                </div>
                            </div>

                        </div>

                        <div class="step-content d-none" data-step-content="2">

                            <ul class="nav nav-tabs mb-3">
                                @foreach ($langs as $index => $lang)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#tab_lang_{{ $lang->code }}">
                                            {{ $lang->name }}
                                            @error('translations.' . $lang->code . '.name')
                                                <span class="text-danger ms-1">•</span>
                                            @enderror
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content">
                                @foreach ($langs as $index => $lang)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                         id="tab_lang_{{ $lang->code }}">

                                        <input type="hidden" name="translations[{{ $lang->code }}][lang_id]" value="{{ $lang->id }}" required />

                                        <div class="mb-3">
                                            <label for="name_{{ $lang->code }}" class="form-label">
                                                {{ __('v1/backend/views/pages/currencies/edit.nom') }}
                                                <span class="text-muted">({{ $lang->name }})</span>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="translations[{{ $lang->code }}][name]" id="name_{{ $lang->code }}" required class="form-control @error('translations.' . $lang->code . '.name') is-invalid @enderror" placeholder="{{ __('v1/backend/views/pages/currencies/edit.exDevise') }}" value="{{ old('translations.' . $lang->code . '.name', getTranslation($currency, $lang->id)?->name) }}" maxlength="100" />
                                            @error('translations.' . $lang->code . '.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="description_{{ $lang->code }}" class="form-label">
                                                {{ __('v1/backend/views/pages/currencies/edit.description') }}
                                                <span class="text-muted">({{ $lang->name }})</span>
                                            </label>
                                            <textarea name="translations[{{ $lang->code }}][description]" id="description_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.description') is-invalid @enderror" rows="3" placeholder="{{ __('v1/backend/views/pages/currencies/edit.descriptionOptionnelleDeLaDevise') }}">{{ old('translations.' . $lang->code . '.description', getTranslation($currency, $lang->id)?->description) }}</textarea>
                                            @error('translations.' . $lang->code . '.description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary d-none" id="btn-prev">
                                <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/currencies/edit.precedent') }}
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="btn-next">
                                    {{ __('v1/backend/views/pages/currencies/edit.suivant') }} <i class="bx bx-right-arrow-alt ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-success d-none" id="btn-submit">
                                    <i class="bx bx-save me-1"></i> {{ __('v1/backend/views/pages/currencies/edit.mettreAJour') }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-currency-form');
        const totalSteps = 2;
        let currentStep  = 1;

        const stepContents = document.querySelectorAll('[data-step-content]');
        const stepItems    = document.querySelectorAll('[data-step]');
        const btnPrev      = document.getElementById('btn-prev');
        const btnNext      = document.getElementById('btn-next');
        const btnSubmit    = document.getElementById('btn-submit');

        function goToStep(step) {
            stepContents.forEach(el => el.classList.add('d-none'));
            document.querySelector(`[data-step-content="${step}"]`).classList.remove('d-none');

            stepItems.forEach(el => {
                el.classList.remove('active', 'done');
                const s = parseInt(el.dataset.step);
                if (s < step)   el.classList.add('done');
                if (s === step) el.classList.add('active');
            });

            btnPrev.classList.toggle('d-none', step === 1);
            btnNext.classList.toggle('d-none', step === totalSteps);
            btnSubmit.classList.toggle('d-none', step !== totalSteps);

            currentStep = step;
        }

        function validateStep(step) {
            const stepContent = document.querySelector(`[data-step-content="${step}"]`);
            const fields = stepContent.querySelectorAll('input, textarea, select');
            const invalidField = Array.from(fields).find((field) => field.willValidate && !field.disabled && !field.checkValidity());

            if (invalidField) {
                invalidField.reportValidity();
                invalidField.focus();
                return false;
            }

            return true;
        }

        function findFirstInvalidField() {
            return form.querySelector(':invalid');
        }

        btnNext.addEventListener('click', () => { if (currentStep < totalSteps && validateStep(currentStep)) goToStep(currentStep + 1); });
        btnPrev.addEventListener('click', () => { if (currentStep > 1) goToStep(currentStep - 1); });

        form.addEventListener('submit', (event) => {
            if (!form.reportValidity()) {
                event.preventDefault();
                const invalidField = findFirstInvalidField();

                if (invalidField) {
                    const invalidStep = invalidField.closest('[data-step-content]');

                    if (invalidStep) {
                        const step = parseInt(invalidStep.dataset.stepContent);
                        goToStep(step);
                    }

                    invalidField.focus();
                }
            }
        });

        goToStep(1);
    });
</script>
@endpush

@push('styles')
<style>
    .step-item {
        display: flex;
        align-items: center;
        gap: 8px;
        opacity: 0.4;
    }
    .step-item.active, .step-item.done { opacity: 1; }
    .step-badge {
        width: 30px; height: 30px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 13px;
    }
    .step-item.active .step-badge { background: #0c0c0c; color: #fff; }
    .step-item.done .step-badge   { background: green;   color: #fff; }
    .step-divider {
        flex: 1; height: 2px;
        background: #e9ecef;
        min-width: 40px;
    }
    .step-label { font-size: 13px; font-weight: 500; white-space: nowrap; }
</style>
@endpush

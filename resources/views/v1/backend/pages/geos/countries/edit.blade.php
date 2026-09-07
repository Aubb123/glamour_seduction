@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/countries/edit.pageTitle') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/countries/edit.pageTitle')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('v1/backend/views/pages/geos/countries/edit.modifier') }} — <span class="text-muted">{{ $country->code }}</span>
                    </h5>
                    <a href="{{ route('backend.geocountries.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/geos/countries/edit.retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <div class="d-flex align-items-center mb-4 gap-3">
                        <div class="step-item" data-step="1">
                            <span class="step-badge">1</span>
                            <span class="step-label">{{ __('v1/backend/views/pages/geos/countries/edit.informationsGenerales') }}</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-item" data-step="2">
                            <span class="step-badge">2</span>
                            <span class="step-label">{{ __('v1/backend/views/pages/geos/countries/edit.traductions') }}</span>
                        </div>
                    </div>

                    <form id="edit-country-form" action="{{ route('backend.geocountries.update', ['geoCountry' => $country->uuid]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="step-content" data-step-content="1">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="continent_id" class="form-label">{{ __('v1/backend/views/pages/geos/countries/edit.continent') }} <span class="text-danger">*</span></label>
                                    <select name="continent_id" id="continent_id" required class="form-select @error('continent_id') is-invalid @enderror">
                                        <option value="">{{ __('v1/backend/views/pages/geos/countries/edit.selectionnerUnContinent') }}</option>
                                        @foreach ($continents as $continent)
                                            <option value="{{ $continent->id }}"
                                                {{ old('continent_id', $country->continent_id) == $continent->id ? 'selected' : '' }}>
                                                {{ $continent->active_translation?->name ?? $continent->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('continent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone_code" class="form-label">{{ __('v1/backend/views/pages/geos/countries/edit.indicatifTelephonique') }}</label>
                                    <input type="text" name="phone_code" id="phone_code" class="form-control @error('phone_code') is-invalid @enderror" placeholder="ex: +229, +33..." value="{{ old('phone_code', $country->phone_code) }}" />
                                    @error('phone_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/countries/edit.optionnelCertainsTerritoires') }}</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="country-type-select" class="form-label">{{ __('v1/backend/views/pages/geos/countries/edit.typeEntite') }} <span class="text-danger">*</span></label>
                                    <select name="type" required class="form-select @error('type') is-invalid @enderror" id="country-type-select">
                                        <option value="country" {{ old('type', $country->type) == 'country' ? 'selected' : '' }}>{{ __('v1/backend/views/pages/geos/countries/edit.paysSouverain') }}</option>
                                        <option value="territory" {{ old('type', $country->type) == 'territory' ? 'selected' : '' }}>{{ __('v1/backend/views/pages/geos/countries/edit.territoireDependance') }}</option>
                                        <option value="special_administrative_region" {{ old('type', $country->type) == 'special_administrative_region' ? 'selected' : '' }}>{{ __('v1/backend/views/pages/geos/countries/edit.regionAdministrativeSpeciale') }}</option>
                                        <option value="disputed" {{ old('type', $country->type) == 'disputed' ? 'selected' : '' }}>{{ __('v1/backend/views/pages/geos/countries/edit.contesteReconnaissanceLimitee') }}</option>
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6" id="sovereign-field">
                                    <label for="sovereign_id" class="form-label">{{ __('v1/backend/views/pages/geos/countries/edit.paysDeRattachement') }}</label>
                                    <select name="sovereign_id" id="sovereign_id" class="form-select @error('sovereign_id') is-invalid @enderror">
                                        <option value="">{{ __('v1/backend/views/pages/geos/countries/edit.aucunPaysSouverain') }}</option>
                                        @foreach ($geocountries as $gc)
                                            <option value="{{ $gc->id }}"
                                                {{ old('sovereign_id', $country->sovereign_id) == $gc->id ? 'selected' : '' }}>
                                                {{ $gc->active_translation?->name ?? $gc->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sovereign_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/countries/edit.requisTerritoiresRegions') }}</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="code" class="form-label">{{ __('v1/backend/views/pages/geos/countries/edit.codeIso3') }}</label>
                                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="ex: FRA, USA, BEN..." value="{{ old('code', $country->code) }}" maxlength="3" />
                                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/countries/edit.troisLettresMajuscules') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="code_2" class="form-label">{{ __('v1/backend/views/pages/geos/countries/edit.codeIso2') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="code_2" id="code_2" required class="form-control @error('code_2') is-invalid @enderror" placeholder="ex: FR, US, BJ, GB-ENG..." value="{{ old('code_2', $country->code_2) }}" maxlength="6" />
                                    @error('code_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/countries/edit.deuxLettresMajuscules') }}</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="sort_order" class="form-label">{{ __('v1/backend/views/pages/geos/countries/edit.ordreAffichage') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="sort_order" id="sort_order" required class="form-control @error('sort_order') is-invalid @enderror" placeholder="ex: 10" value="{{ old('sort_order', $country->sort_order) }}" min="0" />
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/countries/edit.utiliseDansListesSelects') }}</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="currency_code" class="form-label">{{ __('v1/backend/views/pages/geos/countries/edit.codeMonetaire') }}</label>
                                    <input type="text" name="currency_code" id="currency_code" class="form-control @error('currency_code') is-invalid @enderror" placeholder="ex: EUR, USD, XOF..." value="{{ old('currency_code', $country->currency_code) }}" maxlength="3" />
                                    @error('currency_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/countries/edit.troisLettresMajusculesOptionnel') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="currency_symbol" class="form-label">{{ __('v1/backend/views/pages/geos/countries/edit.symboleMonetaire') }}</label>
                                    <input type="text" name="currency_symbol" id="currency_symbol" class="form-control @error('currency_symbol') is-invalid @enderror" placeholder="ex: €, $..." value="{{ old('currency_symbol', $country->currency_symbol) }}" maxlength="10" />
                                    @error('currency_symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/countries/edit.optionnel') }}</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="status" class="form-label">{{ __('Statut') }}</label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="active" {{ old('status', $country->status?->value ?? 'inactive') === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                        <option value="inactive" {{ old('status', $country->status?->value ?? 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                                {{ __('v1/backend/views/pages/geos/countries/edit.nom') }} <span class="text-danger">*</span>
                                                <span class="text-muted">({{ $lang->name }})</span>
                                            </label>
                                            <input type="text" name="translations[{{ $lang->code }}][name]" id="name_{{ $lang->code }}" required class="form-control @error('translations.' . $lang->code . '.name') is-invalid @enderror" placeholder="{{ __('v1/backend/views/pages/geos/countries/edit.exPays') }}" value="{{ old('translations.' . $lang->code . '.name', getTranslation($country, $lang->id)?->name) }}" />
                                            @error('translations.' . $lang->code . '.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="description_{{ $lang->code }}" class="form-label">
                                                {{ __('v1/backend/views/pages/geos/countries/edit.description') }} <span class="text-danger">*</span>
                                                <span class="text-muted">({{ $lang->name }})</span>
                                            </label>
                                            <textarea name="translations[{{ $lang->code }}][description]" id="description_{{ $lang->code }}" required class="form-control @error('translations.' . $lang->code . '.description') is-invalid @enderror" rows="4" placeholder="{{ __('v1/backend/views/pages/geos/countries/edit.descriptionOptionnelleDuPays') }}">{{ old('translations.' . $lang->code . '.description', getTranslation($country, $lang->id)?->description) }}</textarea>
                                            @error('translations.' . $lang->code . '.description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="currency_name_{{ $lang->code }}" class="form-label">
                                                {{ __('v1/backend/views/pages/geos/countries/edit.nomDeLaDevise') }}
                                                <span class="text-muted">({{ $lang->name }})</span>
                                            </label>
                                            <input type="text" name="translations[{{ $lang->code }}][currency_name]" id="currency_name_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.currency_name') is-invalid @enderror" placeholder="ex: Euro, Dollar..." value="{{ old('translations.' . $lang->code . '.currency_name', getTranslation($country, $lang->id)?->currency_name) }}" maxlength="100" />
                                            @error('translations.' . $lang->code . '.currency_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary d-none" id="btn-prev">
                                <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/geos/countries/edit.precedent') }}
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="btn-next">
                                    {{ __('v1/backend/views/pages/geos/countries/edit.suivant') }} <i class="bx bx-right-arrow-alt ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-success d-none" id="btn-submit">
                                    <i class="bx bx-save me-1"></i> {{ __('v1/backend/views/pages/geos/countries/edit.mettreAJour') }}
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
    // ─── Affichage conditionnel du champ "Pays de rattachement" ───
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('country-type-select');
        const sovereignField = document.getElementById('sovereign-field');

        function toggleSovereignField() {
            const val = typeSelect.value;
            // Afficher sovereign_id seulement si le type n'est pas 'country'
            if (val === 'country') {
                sovereignField.style.display = 'none';
            } else {
                sovereignField.style.display = 'block';
            }
        }

        // État initial
        toggleSovereignField();

        // Au changement
        typeSelect.addEventListener('change', toggleSovereignField);
    });

    const form = document.getElementById('edit-country-form');
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
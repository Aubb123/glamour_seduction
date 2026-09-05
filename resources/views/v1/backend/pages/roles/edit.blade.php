@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Modifier un rôle') }} — {{ $role->active_translation?->label ?? 'undefined' }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Modifier un rôle')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('Modifier') }} — <span class="text-muted">{{ $role->active_translation?->label ?? 'undefined' }}</span>
                    </h5>
                    <a href="{{ route('backend.roles.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <div class="d-flex align-items-center mb-4 gap-3">
                        <div class="step-item" data-step="1">
                            <span class="step-badge">1</span>
                            <span class="step-label">{{ __('Informations générales') }}</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-item" data-step="2">
                            <span class="step-badge">2</span>
                            <span class="step-label">{{ __('Traductions') }}</span>
                        </div>
                    </div>

                    <form id="edit-role-form" action="{{ route('backend.roles.update', ['role' => $role->uuid]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="step-content" data-step-content="1">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Nom interne') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="ex: administrator, moderator..." value="{{ old('name', $role->name) }}" disabled />
                                    <input type="hidden" name="name" value="{{ old('name', $role->name) }}" required />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('Nom technique utilisé par Spatie Permission (lettres minuscules, sans espaces).') }}</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Guard') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="guard_name" class="form-control @error('guard_name') is-invalid @enderror" placeholder="ex: web" value="{{ old('guard_name', $role->guard_name) }}" disabled />
                                    <input type="hidden" name="guard_name" value="{{ old('guard_name', $role->guard_name) }}" required />
                                    @error('guard_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('Nom du guard (web par défaut).') }}</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('Statut') }}</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $role->status?->value ?? 'inactive') === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                    <option value="inactive" {{ old('status', $role->status?->value ?? 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                        </div>

                        <div class="step-content d-none" data-step-content="2">

                            <ul class="nav nav-tabs mb-3">
                                @foreach ($langs as $index => $lang)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#tab_lang_{{ $lang->code }}">
                                            {{ $lang->name }}
                                            @error('translations.' . $lang->code . '.label')
                                                <span class="text-danger ms-1">•</span>
                                            @enderror
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content">
                                @foreach ($langs as $index => $lang)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="tab_lang_{{ $lang->code }}">

                                        <input type="hidden" name="translations[{{ $lang->code }}][lang_id]" value="{{ $lang->id }}" required />

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="label_{{ $lang->code }}" class="form-label">
                                                    {{ __('Libellé') }} <span class="text-danger">*</span>
                                                    <span class="text-muted">({{ $lang->name }})</span>
                                                </label>
                                                <input type="text" name="translations[{{ $lang->code }}][label]" id="label_{{ $lang->code }}" required class="form-control @error('translations.' . $lang->code . '.label') is-invalid @enderror" placeholder="{{ __('ex: Administrateur, Éditeur...') }}" value="{{ old('translations.' . $lang->code . '.label', getTranslation($role, $lang->id)?->label) }}" />
                                                @error('translations.' . $lang->code . '.label')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
    
                                        </div>

                                        <div class="mb-3">
                                            <label for="description_{{ $lang->code }}" class="form-label">
                                                {{ __('Description') }} <span class="text-danger">*</span>
                                                <span class="text-muted">({{ $lang->name }})</span>
                                            </label>
                                            <textarea name="translations[{{ $lang->code }}][description]" id="description_{{ $lang->code }}" required class="form-control @error('translations.' . $lang->code . '.description') is-invalid @enderror" rows="4" placeholder="{{ __('Description de ce rôle...') }}">{{ old('translations.' . $lang->code . '.description', getTranslation($role, $lang->id)?->description) }}</textarea>
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
                                <i class="bx bx-arrow-back me-1"></i> {{ __('Précédent') }}
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="btn-next">
                                    {{ __('Suivant') }} <i class="bx bx-arrow-right ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-success d-none" id="btn-submit">
                                    <i class="bx bx-save me-1"></i> {{ __('Mettre à jour') }}
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
    const form = document.getElementById('edit-role-form');
    const totalSteps = 2;
    let currentStep = 1;

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
            if (s < step)  el.classList.add('done');
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

    btnNext.addEventListener('click', () => {
        if (currentStep < totalSteps && validateStep(currentStep)) goToStep(currentStep + 1);
    });

    btnPrev.addEventListener('click', () => {
        if (currentStep > 1) goToStep(currentStep - 1);
    });

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
    .step-item.done .step-badge   { background: green; color: #fff; }
    .step-divider {
        flex: 1; height: 2px;
        background: #e9ecef;
        min-width: 40px;
    }
    .step-label { font-size: 13px; font-weight: 500; white-space: nowrap; }
</style>
@endpush
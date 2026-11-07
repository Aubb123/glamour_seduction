@extends('v1.backend.layouts.app')

@section('title') Modifier le poste — {{ $position->active_translation?->name ?? '' }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Modifier le poste'])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Modifier le poste — {{ $position->active_translation?->name ?? '' }}</h5>
                    <a href="{{ route('backend.positions.index') }}" class="btn btn-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i> Retour</a>
                </div>
                <div class="card-body">

                    {{-- Stepper Nav --}}
                    <div class="d-flex align-items-center mb-4 gap-3">
                        <div class="step-item active" data-step="1">
                            <span class="step-badge">1</span>
                            <span class="step-label">Informations générales</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-item" data-step="2">
                            <span class="step-badge">2</span>
                            <span class="step-label">Traductions</span>
                        </div>
                    </div>

                    <form id="edit-position-form" action="{{ route('backend.positions.update', ['position' => $position->uuid]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- STEP 1 — Informations générales --}}
                        <div class="step-content" data-step-content="1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $position->code) }}" required />
                                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="context" class="form-label">Portée <span class="text-danger">*</span></label>
                                    <select name="context" id="context" class="form-select @error('context') is-invalid @enderror">
                                        <option value="shop" {{ old('context', $position->context?->value) === 'shop' ? 'selected' : '' }}>Boutique</option>
                                        <option value="warehouse" {{ old('context', $position->context?->value) === 'warehouse' ? 'selected' : '' }}>Entrepôt</option>
                                        <option value="both" {{ old('context', $position->context?->value) === 'both' ? 'selected' : '' }}>Boutique et entrepôt</option>
                                    </select>
                                    @error('context') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="status" class="form-label">Statut</label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="active" {{ old('status', $position->status?->value) === 'active' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactive" {{ old('status', $position->status?->value) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="sort_order" class="form-label">Ordre de tri <span class="text-danger">*</span></label>
                                    <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $position->sort_order) }}" min="0" required />
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- STEP 2 — Traductions --}}
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
                                    @php $translation = $position->translations->firstWhere('lang_id', $lang->id); @endphp
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="tab_lang_{{ $lang->code }}">
                                        <input type="hidden" name="translations[{{ $lang->code }}][lang_id]" value="{{ $lang->id }}" />
                                        <div class="mb-3">
                                            <label for="name_{{ $lang->code }}" class="form-label">Nom <span class="text-danger">*</span> <span class="text-muted">({{ $lang->name }})</span></label>
                                            <input type="text" name="translations[{{ $lang->code }}][name]" id="name_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.name') is-invalid @enderror" value="{{ old('translations.' . $lang->code . '.name', $translation?->name) }}" required />
                                            @error('translations.' . $lang->code . '.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small class="text-muted">Le slug sera généré automatiquement.</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description_{{ $lang->code }}" class="form-label">Description <span class="text-muted">({{ $lang->name }})</span></label>
                                            <textarea name="translations[{{ $lang->code }}][description]" id="description_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.description') is-invalid @enderror" rows="3">{{ old('translations.' . $lang->code . '.description', $translation?->description) }}</textarea>
                                            @error('translations.' . $lang->code . '.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Boutons navigation --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary d-none" id="btn-prev">
                                <i class="bx bx-arrow-back me-1"></i> Précédent
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <a href="{{ route('backend.positions.show', ['position' => $position->uuid, 'slug' => $position->active_translation?->slug ?? 'null']) }}" class="btn btn-secondary"><i class="bx bx-x me-1"></i> Annuler</a>
                                <button type="button" class="btn btn-primary" id="btn-next">
                                    Suivant <i class="bx bx-right-arrow-alt ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-success d-none" id="btn-submit">
                                    <i class="bx bx-save me-1"></i> Mettre à jour
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
    const form = document.getElementById('edit-position-form');
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
        if (currentStep < totalSteps && validateStep(currentStep)) {
            goToStep(currentStep + 1);
        }
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

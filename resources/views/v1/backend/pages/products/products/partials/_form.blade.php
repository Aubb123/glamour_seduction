<form id="{{ isset($product) ? 'edit-product-form' : 'create-product-form' }}" action="{{ isset($product) ? route('backend.products.update', ['product' => $product->uuid]) : route('backend.products.store') }}" method="POST">
    @csrf
    @if (isset($product)) @method('PUT') @endif

    <div class="d-flex align-items-center mb-4 gap-3">
        <div class="step-item active" data-step="1">
            <span class="step-badge">1</span>
            <span class="step-label">Informations générales</span>
        </div>
        <div class="step-divider"></div>
        <div class="step-item" data-step="2">
            <span class="step-badge">2</span>
            <span class="step-label">Prix &amp; Poids</span>
        </div>
        <div class="step-divider"></div>
        <div class="step-item" data-step="3">
            <span class="step-badge">3</span>
            <span class="step-label">Traductions</span>
        </div>
        <div class="step-divider"></div>
        <div class="step-item" data-step="4">
            <span class="step-badge">4</span>
            <span class="step-label">Tags &amp; Options</span>
        </div>
    </div>

    {{-- Étape 1 : Informations générales --}}
    <div class="step-content" data-step-content="1">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="category_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">— Sélectionner une catégorie —</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? null) == $category->id ? 'selected' : '' }}>
                            {{ $category->active_translation?->name ?? ('#'.$category->id) }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="brand_id" class="form-label">Marque</label>
{{-- Étape 2 : Prix & Poids --}}
    <div class="step-content d-none" data-step-content="2">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="base_price" class="form-label">Prix de base <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" name="base_price" id="base_price" class="form-control @error('base_price') is-invalid @enderror" placeholder="0.00" value="{{ old('base_price', $product->base_price ?? null) }}" required />
                @error('base_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">Saisi dans la devise par défaut de la boutique.</small>
            </div>

            <div class="col-md-6 mb-3">
                <label for="compare_at_price" class="form-label">Prix barré</label>
                <input type="number" step="0.01" min="0.01" name="compare_at_price" id="compare_at_price" class="form-control @error('compare_at_price') is-invalid @enderror" placeholder="0.00" value="{{ old('compare_at_price', $product->compare_at_price ?? null) }}" />
                @error('compare_at_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">Doit être supérieur au prix de base (prix barré).</small>
            </div>

            <div class="col-md-6 mb-3">
                <label for="weight" class="form-label">Poids (kg)</label>
                <input type="number" step="0.01" min="0" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror" placeholder="0.00" value="{{ old('weight', $product->weight ?? null) }}" />
                @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
                <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                    <option value="">— Sélectionner une marque —</option>
                    @foreach ($brands as $brand)
{{-- Étape 3 : Traductions --}}
    <div class="step-content d-none" data-step-content="3">
        <ul class="nav nav-tabs mb-3">
            @foreach ($langs as $index => $lang)
                <li class="nav-item">
                    <a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#lang-{{ $lang->id }}">{{ strtoupper($lang->code) }}</a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach ($langs as $index => $lang)
                @php
                    $existing = isset($product) ? $product->translations->firstWhere('lang_id', $lang->id) : null;
                @endphp
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="lang-{{ $lang->id }}">
                    <input type="hidden" name="translations[{{ $index }}][lang_id]" value="{{ $lang->id }}" />
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="translations-{{ $index }}-name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="translations[{{ $index }}][name]" id="translations-{{ $index }}-name" class="form-control @error('translations.'.$index.'.name') is-invalid @enderror" placeholder="Nom du produit" value="{{ old('translations.'.$index.'.name', $existing->name ?? '') }}" required />
                            @error('translations.'.$index.'.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="translations-{{ $index }}-short_description" class="form-label">Description courte <span class="text-danger">*</span></label>
                            <input type="text" name="translations[{{ $index }}][short_description]" id="translations-{{ $index }}-short_description" class="form-control @error('translations.'.$index.'.short_description') is-invalid @enderror" placeholder="Résumé (max 500 car.)" value="{{ old('translations.'.$index.'.short_description', $existing->short_description ?? '') }}" required />
                            @error('translations.'.$index.'.short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="translations-{{ $index }}-description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="translations[{{ $index }}][description]" id="translations-{{ $index }}-description" rows="4" class="form-control @error('translations.'.$index.'.description') is-invalid @enderror" placeholder="Description complète du produit" required>{{ old('translations.'.$index.'.description', $existing->description ?? '') }}</textarea>
                            @error('translations.'.$index.'.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id ?? null) == $brand->id ? 'selected' : '' }}>
                            {{ $brand->active_translation?->name ?? ('#'.$brand->id) }}
                        </option>
                    @endforeach
                </select>
                @error('brand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
{{-- Étape 4 : Tags & Options --}}
    <div class="step-content d-none" data-step-content="4">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label d-block">Tags du produit</label>
                <div class="d-flex flex-wrap gap-2">
                    @forelse ($tags as $tag)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tags[]" id="tag-{{ $tag->id }}" value="{{ $tag->id }}"
                                @if (in_array($tag->id, old('tags', $product->tags->pluck('id')->all() ?? []))) checked @endif />
                            <label class="form-check-label" for="tag-{{ $tag->id }}">
                                {{ $tag->active_translation?->name ?? ('#'.$tag->id) }}
                            </label>
                        </div>
                    @empty
                        <span class="text-muted">Aucun tag disponible. Créez-en un dans le module Tags.</span>
                    @endforelse
                </div>
                @error('tags.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="d-flex mt-4 gap-2">
        <button type="button" class="btn btn-secondary d-none" id="btn-prev">
            <i class="bx bx-arrow-back me-1"></i> Précédent
        </button>
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-primary" id="btn-next">
                Suivant <i class="bx bx-right-arrow-alt ms-1"></i>
            </button>
            <button type="submit" class="btn btn-success d-none" id="btn-submit">
                <i class="bx bx-save me-1"></i> {{ isset($product) ? 'Mettre à jour' : 'Enregistrer' }}
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    const form = document.getElementById('{{ isset($product) ? 'edit-product-form' : 'create-product-form' }}');
    const totalSteps = 4;
    let currentStep = 1;

    const stepContents = form.querySelectorAll('[data-step-content]');
    const stepItems    = form.querySelectorAll('[data-step]');
    const btnPrev      = document.getElementById('btn-prev');
    const btnNext      = document.getElementById('btn-next');
    const btnSubmit    = document.getElementById('btn-submit');

    function goToStep(step) {
        stepContents.forEach(el => el.classList.add('d-none'));
        form.querySelector(`[data-step-content="${step}"]`).classList.remove('d-none');

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
        const stepContent = form.querySelector(`[data-step-content="${step}"]`);
        const fields = stepContent.querySelectorAll('input, textarea, select');
        const invalidField = Array.from(fields).find((field) => field.willValidate && !field.disabled && !field.checkValidity());

        if (invalidField) {
            invalidField.reportValidity();
            invalidField.focus();
            return false;
        }

        return true;
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
            const invalidField = form.querySelector(':invalid');
            if (invalidField) {
                const invalidStep = invalidField.closest('[data-step-content]');
                if (invalidStep) goToStep(parseInt(invalidStep.dataset.stepContent));
                invalidField.focus();
            }
        }
    });

    goToStep(1);
</script>
@endpush

            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Statut</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                    @foreach (\App\Enums\Products\Products\ProductStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ old('status', $product->status->value ?? 'draft') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label d-block">Options</label>
                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Mis en avant sur la boutique</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="requires_discreet_packaging" id="requires_discreet_packaging" value="1" {{ old('requires_discreet_packaging', $product->requires_discreet_packaging ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="requires_discreet_packaging">Emballage discret requis</label>
                </div>
            </div>
        </div>
    </div>
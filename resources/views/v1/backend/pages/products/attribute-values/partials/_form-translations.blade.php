<div class="tab-content">
    @foreach ($langs as $index => $lang)
        @php
            $existing = isset($value) ? $value->translations->firstWhere('lang_id', $lang->id) : null;
        @endphp
        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="lang-{{ $lang->id }}">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Langue</label>
                    <input type="hidden" name="translations[{{ $index }}][lang_id]" value="{{ $lang->id }}" />
                    <input type="text" class="form-control" value="{{ $lang->name ?? $lang->code }}" disabled />
                </div>

                <div class="col-md-6 mb-3">
                    <label for="translations-{{ $index }}-label" class="form-label">Libellé <span class="text-danger">*</span></label>
                    <input type="text" name="translations[{{ $index }}][label]" id="translations-{{ $index }}-label" class="form-control @error('translations.'.$index.'.label') is-invalid @enderror" placeholder="ex : Rouge" value="{{ old('translations.'.$index.'.label', $existing?->label) }}" required />
                    @error('translations.'.$index.'.label') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('backend.product-attribute-values.index') }}" class="btn btn-secondary">
        <i class="bx bx-x me-1"></i> Annuler
    </a>
    <button type="submit" class="btn btn-success">
        <i class="bx bx-save me-1"></i> {{ isset($value) ? 'Mettre à jour' : 'Enregistrer' }}
    </button>
</div>
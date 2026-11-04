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
        @php
            $existing = isset($value) ? $value->translations->firstWhere('lang_id', $lang->id) : null;
        @endphp
        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="tab_lang_{{ $lang->code }}">

            <input type="hidden" name="translations[{{ $lang->code }}][lang_id]" value="{{ $lang->id }}" />

            <div class="mb-3">
                <label for="label_{{ $lang->code }}" class="form-label">{{ __('Libellé') }} <span class="text-danger">*</span> <span class="text-muted">({{ $lang->name }})</span></label>
                <input type="text" name="translations[{{ $lang->code }}][label]" id="label_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.label') is-invalid @enderror" placeholder="{{ __('ex : Rouge') }}" value="{{ old('translations.' . $lang->code . '.label', $existing?->label) }}" required />
                @error('translations.' . $lang->code . '.label') <div class="invalid-feedback">{{ ucfirst($lang->name) }} : {{ $message }}</div> @enderror
                <small class="text-muted">{{ __('Le slug sera généré automatiquement.') }}</small>
            </div>
        </div>
    @endforeach
</div>
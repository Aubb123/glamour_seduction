{{-- Champ « Code couleur HEX » : affiché uniquement lorsque l'attribut parent est de type "color". --}}
{{-- Le script _hex-color-script pilote l'affichage et le caractère obligatoire. --}}
@php($currentHex = isset($value) ? $value->hex_color : null)

<div class="col-md-6 mb-3 d-none" id="hex-color-wrapper">
    <label for="hex_color" class="form-label">{{ __('Code couleur') }} <span class="text-danger">*</span></label>
    <div class="input-group">
        <input type="color" id="hex_color_picker" class="form-control form-control-color"
               value="{{ old('hex_color', $currentHex ?? '#000000') }}"
               title="{{ __('Choisir une couleur') }}">
        <input type="text" name="hex_color" id="hex_color"
               class="form-control @error('hex_color') is-invalid @enderror"
               placeholder="#RRGGBB"
               pattern="^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$"
               value="{{ old('hex_color', $currentHex) }}">
        @error('hex_color') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <small class="text-muted">{{ __('Format hexadécimal, ex : #FF0000. Requis pour les attributs de type « Couleur ».') }}</small>
</div>

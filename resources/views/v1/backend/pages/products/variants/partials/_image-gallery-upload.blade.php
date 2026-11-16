@php
    $inputId = $inputId ?? 'variant-files-' . uniqid();
    $images = isset($entity) ? $entity->cloudFiles->where('file_type', 'image')->sortBy(fn ($file) => $file->metadata['page'] ?? 0) : collect();
    $selectedCloudPlatform = old('cloud_platform_uuid', $images->first()?->cloudFolder?->platform?->uuid ?? '');
@endphp

@push('styles')
<style>
    .variant-image-gallery { display:flex; flex-wrap:wrap; gap:10px; }
    .variant-image-card { width:120px; padding:5px; border:1px solid #dee2e6; border-radius:8px; background:#f8f9fa; }
    .variant-image-card img { display:block; width:100%; height:100px; object-fit:cover; border-radius:5px; }
</style>
@endpush

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('Cloud platform') }}</label>
        <select name="cloud_platform_uuid" class="form-select @error('cloud_platform_uuid') is-invalid @enderror">
            <option value="">{{ __('Select a cloud platform') }}</option>
            @foreach (getActiveCloudPlatforms() as $platform)
                <option value="{{ $platform->uuid }}" {{ $selectedCloudPlatform === $platform->uuid ? 'selected' : '' }}>{{ $platform->label }}</option>
            @endforeach
        </select>
        @error('cloud_platform_uuid') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">{{ __('Required only when adding images.') }}</small>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ $form_label ?? __('Variant images') }}</label>
        <input type="file" name="files[]" id="{{ $inputId }}" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="form-control @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror">
        @error('files') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @error('files.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">{{ __('Optional. JPG, PNG, WEBP or GIF; 2 MB maximum per image; four images maximum per variant.') }}</small>
    </div>
</div>

@if ($images->isNotEmpty())
    <div class="mb-3">
        <label class="form-label small fw-medium">{{ __('Current variant images (:count)', ['count' => $images->count()]) }}</label>
        <div class="variant-image-gallery">
            @foreach ($images as $image)
                <div class="variant-image-card">
                    <a href="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}" target="_blank" rel="noopener">
                        <img src="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}" alt="{{ __('Variant image :number', ['number' => $loop->iteration]) }}">
                    </a>

                    {{-- Case à cocher traitée par le formulaire principal (remove_cloud_file_uuids[]).
                         Aucun <form> imbriqué ici : cela casserait le formulaire d'édition. --}}
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox"
                               name="remove_cloud_file_uuids[]" value="{{ $image->uuid }}"
                               id="remove-variant-image-{{ $image->uuid }}">
                        <label class="form-check-label small text-danger" for="remove-variant-image-{{ $image->uuid }}">
                            {{ __("Retirer à l'enregistrement") }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

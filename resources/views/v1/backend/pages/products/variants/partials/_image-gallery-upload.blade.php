@php
    $inputId = $inputId ?? 'variant-files-' . uniqid();
    $images = isset($entity) ? $entity->cloudFiles->where('file_type', 'image')->sortBy(fn ($file) => $file->metadata['page'] ?? 0) : collect();
    $selectedCloudPlatform = old('cloud_platform_uuid', $images->first()?->cloudFolder?->platform?->uuid ?? '');
@endphp

@push('styles')
<style>
    .image-gallery-wrapper { display: flex; flex-wrap: wrap; gap: 8px; }
    .image-gallery-item { position: relative; width: 100px; height: 100px; overflow: hidden; border: 1px solid #dee2e6; border-radius: 8px; background: #f8f9fa; }
    .image-gallery-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
</style>
@endpush

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('Plateforme cloud') }}</label>
        <select name="cloud_platform_uuid" class="form-select @error('cloud_platform_uuid') is-invalid @enderror" {{ $images->isEmpty() ? '' : '' }}>
            <option value="">{{ __('Sélectionnez une plateforme cloud') }}</option>
            @foreach(getActiveCloudPlatforms() as $platform)
                <option value="{{ $platform->uuid }}" {{ $selectedCloudPlatform === $platform->uuid ? 'selected' : '' }}>{{ $platform->label }}</option>
            @endforeach
        </select>
        @error('cloud_platform_uuid') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">{{ __('Requis uniquement si vous ajoutez des images.') }}</small>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __($form_label ?? __('Images de la variante')) }}</label>
        <input type="file" name="files[]" id="{{ $inputId }}" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="form-control @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror" />
        @error('files') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @error('files.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">{{ __('Facultatif — JPG, PNG, WEBP ou GIF, 2 Mo maximum chacune. 4 images maximum au total par variante.') }}</small>
    </div>
</div>

@if ($images->isNotEmpty())
    <div class="mb-3">
        <label class="form-label small fw-medium">{{ __('Images actuelles (:count)', ['count' => $images->count()]) }}</label>
        <div class="image-gallery-wrapper">
            @foreach ($images as $image)
                <div class="image-gallery-item">
                    <img src="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}" alt="{{ __('Image :number', ['number' => $loop->iteration]) }}">
                </div>
            @endforeach
        </div>
        <small class="text-muted d-block mt-1">{{ __('Les images existantes sont conservées. Pour en retirer une, utilisez la fiche de la variante.') }}</small>
    </div>
@endif

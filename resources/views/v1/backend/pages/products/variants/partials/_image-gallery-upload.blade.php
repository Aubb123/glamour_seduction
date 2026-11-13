@php
    $inputId = $inputId ?? 'variant-files-' . uniqid();
    $images = isset($entity) ? $entity->cloudFiles->where('file_type', 'image')->sortBy(fn ($file) => $file->metadata['page'] ?? 0) : collect();
    $selectedCloudPlatform = old('cloud_platform_uuid', $images->first()?->cloudFolder?->platform?->uuid ?? '');
    $removeSelected = old('remove_cloud_file_uuids', []);
@endphp

@push('styles')
<style>
    .image-gallery-wrapper { display: flex; flex-wrap: wrap; gap: 8px; }
    .image-gallery-item { position: relative; width: 120px; overflow: hidden; border: 1px solid #dee2e6; border-radius: 8px; background: #f8f9fa; }
    .image-gallery-item img { width: 100%; height: 100px; object-fit: cover; display: block; }
</style>
@endpush

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('Plateforme cloud') }}</label>
        <select name="cloud_platform_uuid" class="form-select @error('cloud_platform_uuid') is-invalid @enderror">
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
                <div class="image-gallery-item p-1">
                    <img src="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}" alt="{{ __('Image :number', ['number' => $loop->iteration]) }}">
                    <form action="{{ route('backend.product-variants.destroy_file', ['productVariant' => $entity->uuid, 'cloudFile' => $image->uuid]) }}" method="POST" class="text-center mt-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger py-0" onclick="return confirm('{{ __('Retirer cette image de la variante ?') }}')">{{ __('Retirer') }}</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endif

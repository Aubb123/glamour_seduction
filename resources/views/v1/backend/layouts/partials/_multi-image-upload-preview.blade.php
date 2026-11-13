@php
    $inputId = $inputId ?? 'file-input-' . uniqid();
@endphp

@push('styles')
<style>
    .image-gallery-wrapper { display: flex; flex-wrap: wrap; gap: 8px; }
    .image-gallery-item { position: relative; width: 100px; height: 100px; overflow: hidden; border: 1px solid #dee2e6; border-radius: 8px; background: #f8f9fa; }
    .image-gallery-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .image-gallery-item .btn-remove { position: absolute; top: 4px; right: 4px; width: 24px; height: 24px; border-radius: 50%; background: rgba(0,0,0,0.6); color: #fff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; line-height: 1; opacity: 0.8; }
    .image-gallery-item .btn-remove:hover { opacity: 1; background: #dc3545; }
    .image-gallery-empty { color: #6c757d; font-size: 0.875rem; }
</style>
@endpush

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Plateforme cloud actif</label>
        <select name="cloud_platform_uuid" class="form-select @error('cloud_platform_uuid') is-invalid @enderror" {{ isset($cloudPlatformRequired) && $cloudPlatformRequired ? 'required' : '' }}>
            <option value="">{{ __('Sélectionnez une plateforme cloud') }}</option>
            @foreach(getActiveCloudPlatforms() as $platform)
                @php
                    $selectedPlatform = old('cloud_platform_uuid', isset($entity) && $entity->getFirstImage()['cloud_platform'] ? $entity->getFirstImage()['cloud_platform']->uuid : '');
                @endphp
                <option value="{{ $platform->uuid }}" {{ $selectedPlatform == $platform->uuid ? 'selected' : '' }}>
                    {{ $platform->label }}
                </option>
            @endforeach
        </select>
        @error('cloud_platform_uuid') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __($form_label) }}</label>
        <input type="file" name="files[]" id="{{ $inputId }}" multiple accept="image/*" {{ isset($imageRequired) && $imageRequired ? 'required' : '' }} class="form-control @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror" />
        @error('files') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @error('files.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">{{ __($format_accept) }}</small>
        @if(isset($entity) && $entity->cloudFiles->where('file_type', 'image')->count() > 0)
            <div class="mt-2">
                <label class="form-label small fw-medium">Images actuelles ({{ $entity->cloudFiles->where('file_type', 'image')->count() }})</label>
                <div class="image-gallery-wrapper">
                    @foreach($entity->cloudFiles->where('file_type', 'image')->sortBy(fn($f) => $f->metadata['page'] ?? 0) as $image)
                        <div class="image-gallery-item">
                            <img src="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}" alt="Image {{ $image->metadata['page'] ?? $loop->iteration }}">
                            <button type="button" class="btn-remove" title="{{ __('Retirer cette image') }}">×</button>
                        </div>
                    @endforeach
                </div>
                <p class="image-gallery-empty mt-1">{{ __('Le retrait d\'image se fait depuis la fiche du produit.') }}</p>
            </div>
        @endif
    </div>
</div>
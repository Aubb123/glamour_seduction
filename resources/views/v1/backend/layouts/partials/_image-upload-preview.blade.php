@php
    // Identifiant unique pour le champ file, permet d'éviter les collisions si le partial est inclus plusieurs fois
    $inputId = $inputId ?? 'file-input-' . uniqid();
@endphp

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

    /* Preview image hover effect */
    .image-preview-wrapper img { transition: transform .2s ease; }
    .image-preview-wrapper img:hover { transform: scale(1.05); }

    /* Loading overlay */
    .upload-loading-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.85);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        flex-direction: column;
        gap: 16px;
    }
    .upload-loading-overlay.active {
        display: flex;
    }
    .upload-loading-overlay .spinner {
        width: 48px; height: 48px;
        border: 4px solid #e9ecef;
        border-top-color: #0c0c0c;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .upload-loading-overlay .loading-text {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }
    .upload-loading-overlay .loading-subtext {
        font-size: 13px;
        color: #666;
    }
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
        <input type="file" name="image" id="{{ $inputId }}" class="form-control @error('image') is-invalid @enderror" accept="image/*" {{ isset($imageRequired) && $imageRequired ? 'required' : '' }} />
        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">{{ __($format_accept) }}</small>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('Aperçu') }}</label>
        <div class="image-preview-wrapper border rounded d-flex align-items-center justify-content-center bg-light" style="width: 200px; height: 200px; overflow: hidden;">
            @if(isset($entity) && $entity->getFirstImage()['url_img'])
                <img id="preview-img-{{ $inputId }}" src="{{ $entity->getFirstImage()['url_img'] }}" alt="{{ __('Image actuelle') }}" class="img-fluid" style="max-width: 100%; max-height: 100%; object-fit: cover;" />
                <span id="preview-placeholder-{{ $inputId }}" class="text-muted d-none">{{ __('Aucune image sélectionnée') }}</span>
            @else
                <img id="preview-img-{{ $inputId }}" src="#" alt="{{ __('Aperçu de l\'image') }}" class="d-none img-fluid" style="max-width: 100%; max-height: 100%; object-fit: cover;" />
                <span id="preview-placeholder-{{ $inputId }}" class="text-muted">{{ __('Aucune image sélectionnée') }}</span>
            @endif
        </div>
    </div>
</div>

<!-- Loading overlay -->
<div class="upload-loading-overlay" id="upload-overlay-{{ $inputId }}">
    <div class="spinner"></div>
    <div class="loading-text">{{ __('Upload en cours...') }}</div>
    <div class="loading-subtext">{{ __('Veuillez patienter, votre fichier est en cours de transfert.') }}</div>
</div>

@push('scripts')
<script>
    document.getElementById('{{ $inputId }}').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewImg = document.getElementById('preview-img-{{ $inputId }}');
        const placeholder = document.getElementById('preview-placeholder-{{ $inputId }}');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImg.src = event.target.result;
                previewImg.classList.remove('d-none');
                placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            previewImg.src = '#';
            previewImg.classList.add('d-none');
            placeholder.classList.remove('d-none');
        }
    });

    // Intercepter la soumission du formulaire parent pour afficher le loader
    (function() {
        const fileInput = document.getElementById('{{ $inputId }}');
        const form = fileInput.closest('form');
        const overlay = document.getElementById('upload-overlay-{{ $inputId }}');

        if (form && overlay) {
            form.addEventListener('submit', function(e) {
                // Vérifier si un fichier a été sélectionné dans CE champ
                if (fileInput.files.length > 0) {
                    overlay.classList.add('active');
                }
            });
        }
    })();
</script>
@endpush
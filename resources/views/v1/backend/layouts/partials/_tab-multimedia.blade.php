<div class="tab-pane fade" id="tab_multimedia">
    <div class="row g-4">

        <div class="col-md-6">
            <div class="card border h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 fw-bold">
                        <i class="bx bx-image me-2 text-primary"></i>{{ __('Image de la catégorie') }}
                    </h6>
                </div> 
                
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    @if ($entity->getFirstImage()['url_img'])
                        <a href="{{ $entity->getFirstImage()['url_img'] }}" ttarget="_blank" rrel="noopener noreferrer">
                            <img src="{{ $entity->getFirstImage()['url_img'] }}" alt="{{ $entity->active_translation?->name ?? __('Image catégorie') }}" class="img-fluid rounded shadow-sm" style="max-width: 100%; max-height: 300px; object-fit: cover;">
                        </a>
                        
                        @if(isset($entity->getFirstImage()['cloud_file']))
                            <!-- Un petit bouton détail qui mène à backend.cloud-files.show -->
                            <a href="{{ route('backend.cloud-files.show', ['cloudFile' => $entity->getFirstImage()['cloud_file']->uuid]) }}" class="btn btn-sm btn-soft-primary mt-3">
                                <i class="bx bx-info-circle me-1"></i> {{ __('Détails du fichier') }}
                            </a>
                        @endif

                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-image" style="font-size: 4rem; color: #dee2e6;"></i>
                            <p class="text-muted mt-3 mb-0">{{ __('Aucune image définie pour cette catégorie.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 fw-bold">
                        <i class="bx bx-video me-2 text-primary"></i>{{ __('Vidéos') }}
                    </h6>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="text-center py-5">
                        <i class="bx bx-video" style="font-size: 4rem; color: #dee2e6;"></i>
                        <p class="text-muted mt-3 mb-0">{{ __('Aucune vidéo associée pour le moment.') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
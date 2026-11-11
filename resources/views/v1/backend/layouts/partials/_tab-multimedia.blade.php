<div class="tab-pane fade" id="tab_multimedia">
    <div class="row g-4">

        @php
            $allImages = $entity->cloudFiles->where('file_type', 'image')->sortBy(fn($f) => $f->metadata['page'] ?? 0);
            $hasMultipleImages = $allImages->count() > 1;
        @endphp

        <!-- Galerie d'images -->
        <div class="col-md-12">
            <div class="card border h-100">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="bx bx-image me-2 text-primary"></i>{{ __('Images du produit') }}
                    </h6>
                    @if($hasMultipleImages)
                        <span class="badge bg-primary">{{ $allImages->count() }} {{ __('images') }}</span>
                    @endif
                </div>

                <div class="card-body">
                    @if($allImages->isNotEmpty())
                        <div class="row g-2">
                            @foreach($allImages as $image)
                                <div class="col-md-3 col-sm-4 col-6">
                                    <div class="position-relative border rounded overflow-hidden bg-light" style="height:150px;">
                                        <a href="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}" target="_blank" rel="noopener noreferrer" class="d-block w-100 h-100">
                                            <img src="{{ route('cloud-file.show', ['uuid' => $image->uuid]) }}" alt="{{ __('Image') }} {{ $image->metadata['page'] ?? $loop->iteration }}" class="w-100 h-100" style="object-fit: cover;">
                                        </a>

                                        @if($hasMultipleImages)
                                            <form action="{{ route('backend.products.destroy_file', ['product' => $entity->uuid, 'cloudFile' => $image->uuid]) }}" method="POST" class="position-absolute top-0 end-0 m-2" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir retirer cette image ?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="{{ __('Retirer cette image') }}"><i class="bx bx-trash"></i></button>
                                            </form>
                                        @endif

                                        <div class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-50 text-white text-center small py-1">
                                            {{ __('Page') }} {{ $image->metadata['page'] ?? $loop->iteration }}
                                        </div>

                                        @if(isset($image->cloud_file))
                                            <a href="{{ route('backend.cloud-files.show', ['cloudFile' => $image->cloud_file->uuid]) }}" class="position-absolute top-0 start-0 m-2 btn btn-sm btn-soft-primary" title="{{ __('Détails du fichier') }}"><i class="bx bx-info-circle"></i></a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if(!$hasMultipleImages)
                            <div class="mt-3 text-center">
                                <p class="text-muted small">{{ __('Une seule image est associée à ce produit. Ajoutez-en d\'autres depuis la page de modification.') }}</p>
                            </div>
                        @endif

                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-image" style="font-size: 4rem; color: #dee2e6;"></i>
                            <p class="text-muted mt-3 mb-0">{{ __('Aucune image définie pour ce produit.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Vidéos -->
        <div class="col-md-12">
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

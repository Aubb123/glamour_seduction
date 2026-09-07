@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du fichier') }} — <code>{{ $file->file_name }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail du fichier')])

       @include('v1.backend.pages.clouds.files.partials._show-start', ['file' => $file])

                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                                <i class="bx bx-info-circle me-1"></i> {{ __('Informations générales') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_preview">
                                <i class="bx bx-show me-1"></i> {{ __('Prévisualisation') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_entity">
                                <i class="bx bx-link me-1"></i> {{ __('Entité associée') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_metadata">
                                <i class="bx bx-data me-1"></i> {{ __('Métadonnées') }}
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <!-- Prévisualisation -->
                        <div class="tab-pane fade" id="tab_preview">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="card border">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bx bx-show me-2 text-primary"></i>{{ __('Prévisualisation du fichier') }}
                                            </h6>
                                        </div>
                                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                            @if($file->file_type === 'image')
                                                <a href="{{ route('cloud-file.show', ['uuid' => $file->uuid]) }}" target="_blank" rel="noopener noreferrer">
                                                    <img src="{{ route('cloud-file.show', ['uuid' => $file->uuid]) }}" alt="{{ $file->file_name }}" class="img-fluid rounded shadow-sm" style="max-width: 100%; max-height: 500px; object-fit: contain;">
                                                </a>
                                                <small class="text-muted mt-3">
                                                    <i class="bx bx-zoom-in me-1"></i>{{ __('Cliquez sur l\'image pour l\'agrandir') }}
                                                </small>
                                            @elseif($file->file_type === 'video')
                                                <video controls class="w-100 rounded shadow-sm" style="max-height: 500px;">
                                                    <source src="{{ route('cloud-file.show', ['uuid' => $file->uuid]) }}" type="{{ $file->mime_type ?: 'video/mp4' }}">
                                                    {{ __('Votre navigateur ne supporte pas la balise vidéo.') }}
                                                </video>
                                            @elseif($file->file_type === 'zip')
                                                <div class="text-center py-5">
                                                    <i class="bx bx-archive" style="font-size: 5rem; color: #dee2e6;"></i>
                                                    <p class="text-muted mt-3 mb-0">{{ __('La prévisualisation n\'est pas disponible pour les archives ZIP.') }}</p>
                                                    <a href="{{ route('cloud-file.show', ['uuid' => $file->uuid]) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-soft-primary mt-3">
                                                        <i class="bx bx-download me-1"></i> {{ __('Télécharger') }}
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-center py-5">
                                                    <i class="bx bx-file" style="font-size: 5rem; color: #dee2e6;"></i>
                                                    <p class="text-muted mt-3 mb-0">{{ __('La prévisualisation n\'est pas disponible pour ce type de fichier.') }}</p>
                                                    <a href="{{ route('cloud-file.show', ['uuid' => $file->uuid]) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-soft-primary mt-3">
                                                        <i class="bx bx-download me-1"></i> {{ __('Télécharger') }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations générales -->
                        <div class="tab-pane fade show active" id="tab_general">
                            <div class="row g-4">

                                <div class="col-md-8">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bx bx-file me-2 text-primary"></i>{{ __('Détails') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                                <div class="col-8"><code>{{ $file->uuid }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Nom du fichier') }}</div>
                                                <div class="col-8 fw-bold">{{ $file->file_name }}</div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Type') }}</div>
                                                <div class="col-8">
                                                    <span class="badge {{ $file->file_type_badge }}">{{ $file->file_type_label }}</span>
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Taille') }}</div>
                                                <div class="col-8">{{ $file->formatted_size }}</div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Type MIME') }}</div>
                                                <div class="col-8"><code>{{ $file->mime_type }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Chemin distant (cloud)') }}</div>
                                                <div class="col-8">
                                                    @if($file->remote_path)
                                                        <code>{{ $file->remote_path }}</code>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('ID fichier') }}</div>
                                                <div class="col-8"><code>{{ $file->cloud_file_id ?? '—' }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Statut de synchronisation') }}</div>
                                                <div class="col-8">
                                                    @if($file->sync_status === \App\Enums\Clouds\Files\CloudFileStatus::Pending)
                                                        <span class="badge badge-soft-warning">
                                                            <i class="bx bx-time-five me-1"></i> {{ __('En attente') }}
                                                        </span>
                                                    @elseif($file->sync_status === \App\Enums\Clouds\Files\CloudFileStatus::Processing)
                                                        <span class="badge badge-soft-info">
                                                            <i class="bx bx-refresh me-1"></i> {{ __('En cours') }}
                                                        </span>
                                                    @elseif($file->sync_status === \App\Enums\Clouds\Files\CloudFileStatus::Error)
                                                        <span class="badge badge-soft-danger">
                                                            <i class="bx bx-error-circle me-1"></i> {{ __('Erreur') }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-soft-success">
                                                            <i class="bx bx-check-circle me-1"></i> {{ __('Synchronisé') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Chemin local') }}</div>
                                                <div class="col-8">
                                                    @if($file->local_path)
                                                        <code>{{ $file->local_path }}</code>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Dernière tentative') }}</div>
                                                <div class="col-8">
                                                    @if($file->last_sync_attempt_at)
                                                        @formatDate($file->last_sync_attempt_at, 'D MMMM YYYY - HH:mm:ss')
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Hash') }}</div>
                                                <div class="col-8"><code>{{ $file->hash ?? '—' }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Lien partageable') }}</div>
                                                <div class="col-8">
                                                    @if($file->shareable_link)
                                                        <a href="{{ $file->shareable_link }}" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none">
                                                            <i class="bx bx-link-external me-1"></i>{{ __('Ouvrir') }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bx bx-folder me-2 text-primary"></i>{{ __('Dossier') }}
                                            </h6>
                                        </div>
                                        <div class="card-body d-flex flex-column gap-3">

                                            @if($file->cloudFolder)
                                                <div class="d-flex align-items-center" style="background: #f8f9fa;">
                                                    <div class="avatar-sm"><span class="avatar-title bg-soft-primary text-primary fs-20 rounded-circle">
                                                        <i class="bx bx-folder"></i>
                                                    </span></div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-semibold">{{ $file->cloudFolder->folder_name }}</div>
                                                        <small class="text-muted">{{ $file->cloudFolder->path ?? '—' }}</small>
                                                    </div>
                                                    <a href="{{ route('backend.cloud-folders.show', ['cloudFolder' => $file->cloudFolder->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Voir le dossier') }}">
                                                        <i class="bx bx-link-external"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center" style="background: #f8f9fa;">
                                                    <div class="avatar-sm"><span class="avatar-title bg-soft-secondary text-secondary fs-20 rounded-circle">
                                                        <i class="bx bx-folder"></i>
                                                    </span></div>
                                                    <div>
                                                        <div class="fw-semibold">{{ __('Aucun dossier') }}</div>
                                                        <small class="text-muted">{{ __('Fichier orphelin') }}</small>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($file->cloudFolder?->platform)
                                                <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: #e8f9f0;">
                                                    <div>
                                                        <div class="fw-semibold">{{ __('Plateforme') }}</div>
                                                        <small class="text-muted">{{ $file->cloudFolder->platform->label }}</small>
                                                    </div>
                                                    <span class="badge badge-soft-success px-3 py-2">
                                                        <i class="bx bx-server me-1"></i>{{ $file->cloudFolder->platform->name }}
                                                    </span>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Entité associée -->
                        <div class="tab-pane fade" id="tab_entity">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="card border">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bx bx-link me-2 text-primary"></i>{{ __('Entité associée') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if($file->fileable)
                                                <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: #f8f9fa;">
                                                    <div class="avatar-sm"><span class="avatar-title bg-soft-primary text-primary fs-20 rounded-circle">
                                                        <i class="bx bx-box"></i>
                                                    </span></div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-semibold">{{ class_basename($file->fileable_type) }}</div>
                                                        <small class="text-muted">{{ $file->fileable->uuid ?? '—' }}</small>
                                                    </div>
                                                    @if($file->fileable instanceof \App\Models\User)
                                                        <a href="{{ route('backend.users.show', ['user' => $file->fileable->uuid, 'slug' => $file->fileable->slug]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Voir l\'utilisateur') }}">
                                                            {{ $file->fileable->first_name . ' ' . $file->fileable->last_name }} <i class="bx bx-link-external"></i>
                                                        </a>
                                                    @elseif($file->fileable instanceof \App\Models\BlogPost)
                                                        <a href="{{ route('backend.blog-posts.show', ['blogPost' => $file->fileable->uuid, 'slug' => $file->fileable->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Voir l\'article') }}">
                                                            {{ Str::limit($file->fileable->active_translation?->title ?? 'Article', 30) }} <i class="bx bx-link-external"></i>
                                                        </a>
                                                    @elseif($file->fileable instanceof \App\Models\BlogCategory)
                                                        <a href="{{ route('backend.blog-categories.show', ['blogCategory' => $file->fileable->uuid, 'slug' => $file->fileable->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Voir la catégorie') }}">
                                                            {{ Str::limit($file->fileable->active_translation?->name ?? 'Catégorie', 30) }} <i class="bx bx-link-external"></i>
                                                        </a>
                                                    @elseif($file->fileable instanceof \App\Models\BlogTag)
                                                        <a href="{{ route('backend.blog-tags.show', ['blogTag' => $file->fileable->uuid, 'slug' => $file->fileable->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Voir le tag') }}">
                                                            {{ Str::limit($file->fileable->active_translation?->name ?? 'Tag', 30) }} <i class="bx bx-link-external"></i>
                                                        </a>
                                                    @elseif($file->fileable instanceof \App\Models\Gallery)
                                                        <a href="{{ route('backend.galleries.show', ['gallery' => $file->fileable->uuid, 'slug' => $file->fileable->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Voir la galerie') }}">
                                                            {{ Str::limit($file->fileable->active_translation?->title ?? 'Galerie', 30) }} <i class="bx bx-link-external"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            @else
                                                <p class="text-muted mb-0">{{ __('Aucune entité associée à ce fichier.') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Métadonnées -->
                        <div class="tab-pane fade" id="tab_metadata">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="card border">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bx bx-data me-2 text-primary"></i>{{ __('Métadonnées') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($file->metadata) && is_array($file->metadata))
                                                <div class="table-responsive">
                                                    <table class="table table-centered table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width:200px;">{{ __('Clé') }}</th>
                                                                <th>{{ __('Valeur') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($file->metadata as $key => $value)
                                                                <tr>
                                                                    <td class="fw-semibold"><code>{{ $key }}</code></td>
                                                                    <td>
                                                                        @if(is_array($value) || is_object($value))
                                                                            <pre class="mb-0" style="max-height:150px;overflow:auto;"><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
                                                                        @elseif(is_bool($value))
                                                                            <span>{{ $value ? 'true' : 'false' }}</span>
                                                                        @else
                                                                            <span>{{ $value ?? '—' }}</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted mb-0">{{ __('Aucune métadonnée disponible.') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

        @include('v1.backend.pages.clouds.files.partials._show-end', ['file' => $file])

@endsection
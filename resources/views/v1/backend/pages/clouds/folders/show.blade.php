@extends('v1.backend.layouts.app')

@section('title') {{ __('Détail du dossier') }} — <code>{{ $folder->folder_name }}</code> @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail du dossier')])

    @include('v1.backend.pages.clouds.folders.partials._show-start', ['folder' => $folder])

        <div class="row g-4">
            <div class="col-md-8">
                <div class="card border h-100">
                    <div class="card-header bg-light py-2"><h6 class="mb-0 fw-bold"><i class="bx bx-folder me-2 text-primary"></i>{{ __('Détails') }}</h6></div>
                    <div class="card-body">
                        <div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div><div class="col-8"><code>{{ $folder->uuid }}</code></div></div><hr class="my-2">
                        <div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('Nom') }}</div><div class="col-8 fw-bold">{{ $folder->folder_name }}</div></div><hr class="my-2">
                        <div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('Plateforme') }}</div><div class="col-8">
                            @if($folder->platform)
                                <a href="{{ route('backend.cloud-platforms.show', ['cloudPlatform' => $folder->platform->uuid, 'slug' => $folder->platform->slug]) }}" class="fw-semibold text-primary text-decoration-none">
                                    <i class="bx bx-server me-1"></i>{{ $folder->platform->label }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div></div><hr class="my-2">
                        <div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('Dossier parent') }}</div><div class="col-8">
                            @if($folder->parent)
                                <a href="{{ route('backend.cloud-folders.show', ['cloudFolder' => $folder->parent->uuid]) }}" class="fw-semibold text-primary text-decoration-none">
                                    <i class="bx bx-folder me-1"></i>{{ $folder->parent->folder_name }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div></div><hr class="my-2">
                        <div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('Chemin') }}</div><div class="col-8"><code>{{ $folder->path ?? '—' }}</code></div></div><hr class="my-2">
                        <div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('ID externe') }}</div><div class="col-8"><code>{{ $folder->folder_id ?? '—' }}</code></div></div>
                        <div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('Entité') }}</div><div class="col-8"><code>{{ $folder->entity_table ?? '—' }}</code></div></div>
                        <div class="row mb-3 align-items-center"><div class="col-4 text-muted fw-semibold">{{ __('Type de contenu') }}</div><div class="col-8"><code>{{ $folder->content_type ? implode(', ', $folder->content_type) : '—' }}</code></div></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border h-100">
                    <div class="card-header bg-light py-2"><h6 class="mb-0 fw-bold"><i class="bx bx-pulse me-2 text-primary"></i>{{ __('Statut') }}</h6></div>
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: {{ $folder->status === \App\Enums\Clouds\Folders\CloudFolderStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                            <div>
                                <div class="fw-semibold">{{ __('Statut') }}</div>
                                <small class="text-muted">{{ __('Dossier actif') }}</small>
                            </div>
                            @if ($folder->status === \App\Enums\Clouds\Folders\CloudFolderStatus::Active)
                                <span class="badge bg-soft-success px-3 py-2"><i class="bx bx-check-circle me-1"></i>{{ __('Actif') }}</span>
                            @else
                                <span class="badge bg-soft-danger px-3 py-2"><i class="bx bx-x-circle me-1"></i>{{ __('Inactif') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($folder->children->isNotEmpty())
            <br>
            @include('v1.backend.pages.clouds.folders.partials._index-table', [
                'folders' => $subFolders
            ])
        @endif

        @if ($folder->files->isNotEmpty())
            <br>
            @include('v1.backend.pages.clouds.files.partials._index-table', [
                'files' => $files
            ])
        @endif

    @include('v1.backend.pages.clouds.folders.partials._show-end', ['folder' => $folder])

@endsection
@extends('v1.backend.layouts.app')

@section('title') {{ __('Modifier un dossier cloud') }} — <code>{{ $folder->folder_name }}</code> @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Modifier un dossier cloud')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Modifier') }} — <code>{{ $folder->folder_name }}</code></h5>
                    <a href="{{ route('backend.cloud-folders.index') }}" class="btn btn-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.cloud-folders.update', ['cloudFolder' => $folder->uuid]) }}" method="POST">
                        @csrf @method('PUT')

                        <!-- Champs éditables -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Plateforme') }}</label>
                                <input type="text" class="form-control" value="{{ $folder->platform?->label ?? '—' }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Nom du dossier') }}</label>
                                <input type="text" class="form-control @error('folder_name') is-invalid @enderror" name="folder_name" value="{{ old('folder_name', $folder->folder_name) }}">
                                @error('folder_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Dossier parent') }}</label>
                                <input type="text" class="form-control" value="{{ $folder->parent?->folder_name ?? __('Aucun (dossier racine)') }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Entité') }}</label>
                                <input type="text" class="form-control @error('entity_table') is-invalid @enderror" name="entity_table" value="{{ old('entity_table', $folder->entity_table ?? '') }}" placeholder="{{ __('users, blog_categories, ...') }}">
                                @error('entity_table')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Type de contenu (extensions séparées par des virgules)') }}</label>
                                <input type="text" class="form-control @error('content_type') is-invalid @enderror" name="content_type" value="{{ old('content_type', implode(', ', $folder->content_type ?? [])) }}" placeholder="{{ __('png, jpg, webp') }}">
                                @error('content_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ __('Statut') }}</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $folder->status?->value ?? 'inactive') === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                    <option value="inactive" {{ old('status', $folder->status?->value ?? 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success"><i class="bx bx-save me-1"></i> {{ __('Mettre à jour') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
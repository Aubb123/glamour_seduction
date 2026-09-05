
@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Modifier une plateforme cloud') }} — <code>{{ $platform->name }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Modifier une plateforme cloud')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('Modifier') }} — <code>{{ $platform->name }}</code>
                    </h5>
                    <a href="{{ route('backend.cloud-platforms.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <form id="edit-cloud-platform-form" action="{{ route('backend.cloud-platforms.update', ['cloudPlatform' => $platform->uuid]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="bx bx-info-circle me-2"></i>
                            <span>{{ __('Laissez les champs "Clé API" et "Secret API" vides si vous ne souhaitez pas les modifier.') }}</span>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">{{ __('Nom technique') }} <span class="text-danger">*</span></label>
                                <select name="name" id="name" class="form-select @error('name') is-invalid @enderror" required>
                                    <option value="backblaze_b2" {{ old('name', $platform->name) === 'backblaze_b2' ? 'selected' : '' }}>backblaze_b2</option>
                                </select>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="label" class="form-label">{{ __('Label') }} <span class="text-danger">*</span></label>
                                <input type="text" name="label" id="label" required class="form-control @error('label') is-invalid @enderror" placeholder="ex: Amazon Web Services" value="{{ old('label', $platform->label) }}" />
                                @error('label') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="disk_name" class="form-label">{{ __('Nom du disque') }} <span class="text-danger">*</span></label>
                                <select name="disk_name" id="disk_name" required class="form-select @error('disk_name') is-invalid @enderror">
                                    <option value="b2" {{ old('disk_name', $platform->disk_name) === 'b2' ? 'selected' : '' }}>b2</option>
                                </select>
                                @error('disk_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">{{ __('Nom du disque Filesystem utilisé en interne.') }}</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="api_endpoint" class="form-label">{{ __('API Endpoint') }} <span class="text-danger">*</span></label>
                                <input type="text" name="api_endpoint" id="api_endpoint" required class="form-control @error('api_endpoint') is-invalid @enderror" placeholder="https://api.example.com" value="{{ old('api_endpoint', $platform->api_endpoint ? $platform->api_endpoint : 'https://s3.us-west-004.backblazeb2.com') }}" />
                                @error('api_endpoint') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="api_key" class="form-label">{{ __('Clé API') }}</label>
                                <input type="password" name="api_key" id="api_key" class="form-control @error('api_key') is-invalid @enderror" placeholder="•••••••• (laisser vide pour conserver)" autocomplete="off" />
                                @error('api_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">{{ __('Laisser vide pour conserver la valeur actuelle.') }}</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="api_secret" class="form-label">{{ __('Secret API') }}</label>
                                <input type="password" name="api_secret" id="api_secret" class="form-control @error('api_secret') is-invalid @enderror" placeholder="•••••••• (laisser vide pour conserver)" autocomplete="off" />
                                @error('api_secret') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">{{ __('Laisser vide pour conserver la valeur actuelle.') }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }} <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" required class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="{{ __('Description de la plateforme...') }}">{{ old('description', $platform->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="metadata" class="form-label">{{ __('Métadonnées (JSON)') }}</label>
                                <textarea name="metadata" id="metadata" class="form-control @error('metadata') is-invalid @enderror" rows="3" placeholder='{"region": "us-east-1"}'>{{ old('metadata', is_array($platform->metadata) ? json_encode($platform->metadata) : $platform->metadata) }}</textarea>
                                @error('metadata') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ __('Statut') }}</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $platform->status?->value ?? 'inactive') === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                    <option value="inactive" {{ old('status', $platform->status?->value ?? 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save me-1"></i> {{ __('Mettre à jour') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
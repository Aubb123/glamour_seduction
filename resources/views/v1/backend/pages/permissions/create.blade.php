@extends('v1.backend.layouts.app') 

@section('title') 
    {{ __('Créer une permission') }}
@endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Créer une permission')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Créer une permission') }}</h5>
                    <a href="{{ route('backend.permissions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <form id="create-permission-form" action="{{ route('backend.permissions.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Nom') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" required class="form-control @error('name') is-invalid @enderror" placeholder="ex: articles.create, users.edit..." value="{{ old('name') }}" />
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">{{ __('Lettres minuscules, chiffres, tirets et points. Ex: articles.edit, users.delete') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="guard_name" class="form-label">{{ __('Guard') }} <span class="text-danger">*</span></label>
                            <input type="text" name="guard_name" id="guard_name" required class="form-control @error('guard_name') is-invalid @enderror" placeholder="web" value="{{ old('guard_name', 'web') }}" />
                            @error('guard_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">{{ __('Nom du guard (web par défaut).') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Statut') }}</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', 'inactive') === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                            </select>
                            @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Assigner aux rôles') }}</label>
                            <small class="text-muted d-block mb-2">{{ __('Optionnel. Vous pourrez modifier cela plus tard.') }}</small>
                            <div class="row">
                                @forelse ($roles as $role)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}">
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                {{ $role->active_translation?->label ?? $role->name }}
                                            </label>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <p class="text-muted">{{ __('Aucun rôle disponible. Créez d\'abord des rôles.') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('backend.permissions.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i> {{ __('Annuler') }}
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save me-1"></i> {{ __('Enregistrer') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
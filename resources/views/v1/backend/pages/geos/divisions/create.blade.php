@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/divisions/create.pageTitle') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/divisions/create.pageTitle')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('v1/backend/views/pages/geos/divisions/create.creerUneDivision') }}
                    </h5>
                    <a href="{{ route('backend.geodivisions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/create.retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <div class="d-flex align-items-center mb-4 gap-3">
                        <div class="step-item active" data-step="1">
                            <span class="step-badge">1</span>
                            <span class="step-label">{{ __('v1/backend/views/pages/geos/divisions/create.informationsGenerales') }}</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-item" data-step="2">
                            <span class="step-badge">2</span>
                            <span class="step-label">{{ __('v1/backend/views/pages/geos/divisions/create.traductions') }}</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-item" data-step="3">
                            <span class="step-badge">3</span>
                            <span class="step-label">{{ __('v1/backend/views/pages/geos/divisions/create.confirmation') }}</span>
                        </div>
                    </div>

                    <form id="create-division-form" action="{{ route('backend.geodivisions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- STEP 1 : Informations générales -->
                        <div class="step-content" data-step-content="1">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="country_id" class="form-label">{{ __('v1/backend/views/pages/geos/divisions/create.pays') }} <span class="text-danger">*</span></label>
                                    <select name="country_id" id="country_id" class="form-select @error('country_id') is-invalid @enderror" required>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                {{ $country->active_translation?->name ?? $country->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/divisions/create.paysAide') }}</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="division_type_id" class="form-label">{{ __('v1/backend/views/pages/geos/divisions/create.typeDeDivision') }} <span class="text-danger">*</span></label>
                                    <select name="division_type_id" id="division_type_id" class="form-select @error('division_type_id') is-invalid @enderror" required>
                                        @foreach ($divisionTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('division_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->active_translation?->name ?? $type->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('division_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/divisions/create.typeAide') }}</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="parent_id" class="form-label">{{ __('v1/backend/views/pages/geos/divisions/create.parent') }}</label>
                                    <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                        <option value="">—</option>
                                        @foreach ($countries as $country)
                                            @foreach ($country->divisions as $division)
                                                <option value="{{ $division->id }}" data-country="{{ $country->id }}" {{ old('parent_id') == $division->id ? 'selected' : '' }}>
                                                    {{ $division->active_translation?->name ?? '—' }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/divisions/create.parentAide') }}</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="form-label">{{ __('v1/backend/views/pages/geos/divisions/create.statut') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', 'inactive') === 'active' ? 'selected' : '' }}>{{ __('v1/backend/views/pages/geos/divisions/create.actif') }}</option>
                                        <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('v1/backend/views/pages/geos/divisions/create.inactif') }}</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('v1/backend/views/pages/geos/divisions/create.niveau') }}</label>
                                    <p class="form-control-plaintext">—</p>
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/divisions/create.niveauAide') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="notes" class="form-label">{{ __('v1/backend/views/pages/geos/divisions/create.notes') }}</label>
                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                        </div>

                        <!-- STEP 2 : Traductions -->
                        <div class="step-content d-none" data-step-content="2">

                            <ul class="nav nav-tabs mb-3">
                                @foreach ($langs as $index => $lang)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#tab_lang_{{ $lang->code }}">
                                            {{ $lang->name }}
                                            @error('translations.' . $lang->code . '.name')
                                                <span class="text-danger ms-1">•</span>
                                            @enderror
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content">
                                @foreach ($langs as $index => $lang)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="tab_lang_{{ $lang->code }}">

                                        <input type="hidden" name="translations[{{ $lang->code }}][lang_id]" value="{{ $lang->id }}" required />

                                        <div class="mb-3">
                                            <label for="name_{{ $lang->code }}" class="form-label">
                                                {{ __('v1/backend/views/pages/geos/divisions/create.nom') }} <span class="text-danger">*</span>
                                                <span class="text-muted">({{ $lang->name }})</span>
                                            </label>
                                            <input type="text" name="translations[{{ $lang->code }}][name]" id="name_{{ $lang->code }}" required class="form-control @error('translations.' . $lang->code . '.name') is-invalid @enderror" placeholder="{{ __('v1/backend/views/pages/geos/divisions/create.nomPlaceholder') }}" value="{{ old('translations.' . $lang->code . '.name') }}" maxlength="150" />
                                            @error('translations.' . $lang->code . '.name') <div class="invalid-feedback">{{ ucfirst($lang->name) }} : {{ $message }}</div> @enderror
                                            <small class="text-muted">{{ __('Le slug sera généré automatiquement.') }}</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description_{{ $lang->code }}" class="form-label">
                                                {{ __('v1/backend/views/pages/geos/divisions/create.description') }} <span class="text-danger">*</span>
                                                <span class="text-muted">({{ $lang->name }})</span>
                                            </label>
                                            <textarea name="translations[{{ $lang->code }}][description]" id="description_{{ $lang->code }}" required class="form-control @error('translations.' . $lang->code . '.description') is-invalid @enderror" rows="4" placeholder="{{ __('v1/backend/views/pages/geos/divisions/create.descriptionPlaceholder') }}">{{ old('translations.' . $lang->code . '.description') }}</textarea>
                                            @error('translations.' . $lang->code . '.description') <div class="invalid-feedback">{{ ucfirst($lang->name) }} : {{ $message }}</div> @enderror
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                        </div>

                        <!-- STEP 3 : Récapitulatif -->
                        <div class="step-content d-none" data-step-content="3">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('v1/backend/views/pages/geos/divisions/create.pays') }}</label>
                                    <p class="form-control-plaintext">{{ $countries->firstWhere('id', old('country_id'))?->active_translation?->name ?? '—' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('v1/backend/views/pages/geos/divisions/create.typeDeDivision') }}</label>
                                    <p class="form-control-plaintext">{{ $divisionTypes->firstWhere('id', old('division_type_id'))?->active_translation?->name ?? '—' }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('v1/backend/views/pages/geos/divisions/create.statut') }}</label>
                                    <p class="form-control-plaintext">{{ old('status', 'inactive') === 'active' ? __('v1/backend/views/pages/geos/divisions/create.actif') : __('v1/backend/views/pages/geos/divisions/create.inactif') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('v1/backend/views/pages/geos/divisions/create.niveau') }}</label>
                                    <p class="form-control-plaintext">{{ __('v1/backend/views/pages/geos/divisions/create.niveauAide') }}</p>
                                </div>
                            </div>

                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">{{ __('v1/backend/views/pages/geos/divisions/create.recapitulatif') }}</h6>
                                </div>
                                <div class="card-body">
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/divisions/create.recapitulatifAide') }}</small>
                                </div>
                            </div>

                        </div>

                        <!-- Boutons navigation -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary d-none" id="btn-prev">
                                <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/create.precedent') }}
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="btn-next">
                                    {{ __('v1/backend/views/pages/geos/divisions/create.suivant') }} <i class="bx bx-right-arrow-alt ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-success d-none" id="btn-submit">
                                    <i class="bx bx-save me-1"></i> {{ __('v1/backend/views/pages/geos/divisions/create.enregistrer') }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @include('v1.backend.layouts.partials._stepper-script', ['formId' => 'create-division-form'])
@endpush

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
    .step-item.done .step-badge   { background: green;   color: #fff; }
    .step-divider {
        flex: 1; height: 2px;
        background: #e9ecef;
        min-width: 40px;
    }
    .step-label { font-size: 13px; font-weight: 500; white-space: nowrap; }
</style>
@endpush
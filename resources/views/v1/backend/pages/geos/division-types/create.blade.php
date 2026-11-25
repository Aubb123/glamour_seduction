@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/division-types/create.pageTitle') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/division-types/create.pageTitle')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('v1/backend/views/pages/geos/division-types/create.creerUnTypeDeDivision') }}
                    </h5>
                    <a href="{{ route('backend.geodivisiontypes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/create.retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <div class="d-flex align-items-center mb-4 gap-3">
                        <div class="step-item active" data-step="1">
                            <span class="step-badge">1</span>
                            <span class="step-label">{{ __('v1/backend/views/pages/geos/division-types/create.informationsGenerales') }}</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-item" data-step="2">
                            <span class="step-badge">2</span>
                            <span class="step-label">{{ __('v1/backend/views/pages/geos/division-types/create.traductions') }}</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-item" data-step="3">
                            <span class="step-badge">3</span>
                            <span class="step-label">{{ __('v1/backend/views/pages/geos/division-types/create.confirmation') }}</span>
                        </div>
                    </div>

                    <form id="create-division-type-form" action="{{ route('backend.geodivisiontypes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- STEP 1 : Informations générales -->
                        <div class="step-content" data-step-content="1">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="code" class="form-label">{{ __('v1/backend/views/pages/geos/division-types/create.codeTechnique') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="ex : department" value="{{ old('code') }}" maxlength="50" required />
                                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/division-types/create.codeAide') }}</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="level" class="form-label">{{ __('v1/backend/views/pages/geos/division-types/create.niveauParDefaut') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="level" id="level" class="form-control @error('level') is-invalid @enderror" placeholder="1" value="{{ old('level', 1) }}" min="1" required />
                                    @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/division-types/create.niveauAide') }}</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="sort_order" class="form-label">{{ __('v1/backend/views/pages/geos/division-types/create.ordreAffichage') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" placeholder="0" value="{{ old('sort_order', $lastSortOrder > 0 ? $lastSortOrder + 1 : 0) }}" min="0" required />
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    @include('v1.backend.layouts.partials._last-sort-order')
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/division-types/create.ordreAide') }}</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="form-label">{{ __('v1/backend/views/pages/geos/division-types/create.statut') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', 'inactive') === 'active' ? 'selected' : '' }}>{{ __('v1/backend/views/pages/geos/division-types/create.actif') }}</option>
                                        <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('v1/backend/views/pages/geos/division-types/create.inactif') }}</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                                {{ __('v1/backend/views/pages/geos/division-types/create.libelle') }} <span class="text-danger">*</span>
                                                <span class="text-muted">({{ $lang->name }})</span>
                                            </label>
                                            <input type="text" name="translations[{{ $lang->code }}][name]" id="name_{{ $lang->code }}" required class="form-control @error('translations.' . $lang->code . '.name') is-invalid @enderror" placeholder="{{ __('v1/backend/views/pages/geos/division-types/create.libellePlaceholder') }}" value="{{ old('translations.' . $lang->code . '.name') }}" maxlength="100" />
                                            @error('translations.' . $lang->code . '.name') <div class="invalid-feedback">{{ ucfirst($lang->name) }} : {{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="description_{{ $lang->code }}" class="form-label">
                                                {{ __('v1/backend/views/pages/geos/division-types/create.description') }} <span class="text-muted">({{ $lang->name }})</span>
                                            </label>
                                            <textarea name="translations[{{ $lang->code }}][description]" id="description_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.description') is-invalid @enderror" rows="4" placeholder="{{ __('v1/backend/views/pages/geos/division-types/create.descriptionPlaceholder') }}">{{ old('translations.' . $lang->code . '.description') }}</textarea>
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
                                    <label class="form-label">{{ __('v1/backend/views/pages/geos/division-types/create.codeTechnique') }}</label>
                                    <p class="form-control-plaintext">{{ old('code') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('v1/backend/views/pages/geos/division-types/create.niveauParDefaut') }}</label>
                                    <p class="form-control-plaintext">{{ old('level', 1) }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('v1/backend/views/pages/geos/division-types/create.ordreAffichage') }}</label>
                                    <p class="form-control-plaintext">{{ old('sort_order', 0) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('v1/backend/views/pages/geos/division-types/create.statut') }}</label>
                                    <p class="form-control-plaintext">{{ old('status', 'inactive') === 'active' ? __('v1/backend/views/pages/geos/division-types/create.actif') : __('v1/backend/views/pages/geos/division-types/create.inactif') }}</p>
                                </div>
                            </div>

                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">{{ __('v1/backend/views/pages/geos/division-types/create.recapitulatif') }}</h6>
                                </div>
                                <div class="card-body">
                                    <small class="text-muted">{{ __('v1/backend/views/pages/geos/division-types/create.recapitulatifAide') }}</small>
                                </div>
                            </div>

                        </div>

                        <!-- Boutons navigation -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary d-none" id="btn-prev">
                                <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/create.precedent') }}
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="btn-next">
                                    {{ __('v1/backend/views/pages/geos/division-types/create.suivant') }} <i class="bx bx-right-arrow-alt ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-success d-none" id="btn-submit">
                                    <i class="bx bx-save me-1"></i> {{ __('v1/backend/views/pages/geos/division-types/create.enregistrer') }}
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
    @include('v1.backend.layouts.partials._stepper-script', ['formId' => 'create-division-type-form'])
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

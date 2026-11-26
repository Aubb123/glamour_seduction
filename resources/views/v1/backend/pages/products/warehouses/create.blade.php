@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Créer un entrepôt') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Créer un entrepôt')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Créer un entrepôt') }}</h5>
                    <a href="{{ route('backend.product-warehouses.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">

                    {{-- Stepper Nav --}}
                    <div class="d-flex align-items-center mb-4 gap-3">
                        <div class="step-item active" data-step="1">
                            <span class="step-badge">1</span>
                            <span class="step-label">{{ __('Informations générales') }}</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-item" data-step="2">
                            <span class="step-badge">2</span>
                            <span class="step-label">{{ __('Traductions') }}</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-item" data-step="3">
                            <span class="step-badge">3</span>
                            <span class="step-label">{{ __('Localisation & contact') }}</span>
                        </div>
                    </div>

                    <form id="create-warehouse-form" action="{{ route('backend.product-warehouses.store') }}" enctype="multipart/form-data" method="POST">
                        @csrf

                        {{-- STEP 1 --}}
                        <div class="step-content" data-step-content="1">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sort_order" class="form-label">{{ __('Ordre de tri') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" placeholder="0" value="{{ old('sort_order', $lastSortOrder > 0 ? $lastSortOrder + 1 : 0) }}" min="0" required />
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    @include('v1.backend.layouts.partials._last-sort-order')
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">{{ __('Code') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="ex : COT-01" value="{{ old('code') }}" maxlength="30" required />
                                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('Majuscules, chiffres, tirets et underscores. Immuable après création.') }}</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">{{ __('Type') }} <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                        @foreach (\App\Enums\Products\Warehouses\ProductWarehouseType::cases() as $type)
                                            <option value="{{ $type->value }}" {{ old('type', 'storage') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ __('Statut') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                        <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label d-block">{{ __('Entrepôt par défaut') }}</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_default">{{ __('Définir comme entrepôt par défaut') }}</label>
                                    </div>
                                    <small class="text-muted">{{ __('Uniquement pour un entrepôt de stockage actif. Un seul défaut autorisé.') }}</small>
                                    @error('is_default') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                            </div>

                        </div>

                        {{-- STEP 2 --}}
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

                                        <input type="hidden" name="translations[{{ $lang->code }}][lang_id]" value="{{ $lang->id }}" />

                                        <div class="mb-3">
                                            <label for="name_{{ $lang->code }}" class="form-label">{{ __('Nom') }} <span class="text-danger">*</span> <span class="text-muted">({{ $lang->name }})</span></label>
                                            <input type="text" name="translations[{{ $lang->code }}][name]" id="name_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.name') is-invalid @enderror" placeholder="ex : Entrepôt principal Cotonou" value="{{ old('translations.' . $lang->code . '.name') }}" maxlength="150" required />
                                            @error('translations.' . $lang->code . '.name') <div class="invalid-feedback">{{ ucfirst($lang->name) }} : {{ $message }}</div> @enderror
                                            <small class="text-muted">{{ __('Le slug sera généré automatiquement.') }}</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description_{{ $lang->code }}" class="form-label">{{ __('Description') }} <span class="text-danger">*</span> <span class="text-muted">({{ $lang->name }})</span></label>
                                            <textarea name="translations[{{ $lang->code }}][description]" id="description_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.description') is-invalid @enderror" rows="4" placeholder="{{ __('Description de cet entrepôt...') }}" required>{{ old('translations.' . $lang->code . '.description') }}</textarea>
                                            @error('translations.' . $lang->code . '.description') <div class="invalid-feedback">{{ ucfirst($lang->name) }} : {{ $message }}</div> @enderror
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                        </div>


                        {{-- STEP 3 --}}
                        <div class="step-content d-none" data-step-content="3">

                            @php
                                $selectedDivisionId = old('division_id');
                                $selectedCountryId = $divisions->firstWhere('id', $selectedDivisionId)?->country_id;
                            @endphp

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="country_id" class="form-label">{{ __('Pays') }}</label>
                                    <select id="country_id" class="form-select">
                                        <option value="">{{ __('Sélectionner un pays') }}</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}" {{ (int) $selectedCountryId === (int) $country->id ? 'selected' : '' }}>
                                                {{ $country->active_translation?->name ?? $country->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">{{ __('Sélectionnez un pays pour afficher ses divisions.') }}</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="division_id" class="form-label">{{ __('Division') }}</label>
                                    <select name="division_id" id="division_id" class="form-select @error('division_id') is-invalid @enderror">
                                        <option value="">{{ __('Sélectionner une division') }}</option>
                                        @foreach ($divisions as $division)
                                            <option value="{{ $division->id }}" data-country="{{ $division->country_id }}" data-has-children="{{ $division->children_count > 0 ? '1' : '0' }}" {{ (string) $selectedDivisionId === (string) $division->id ? 'selected' : '' }}>
                                                {{ $division->active_translation?->name ?? $division->uuid }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('division_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">{{ __('Ville') }}</label>
                                    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" placeholder="ex : Cotonou" value="{{ old('city') }}" maxlength="150" />
                                    @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="address" class="form-label">{{ __('Adresse') }}</label>
                                    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="ex : Zone industrielle de Fidjrossè" value="{{ old('address') }}" maxlength="255" />
                                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="latitude" class="form-label">{{ __('Latitude') }}</label>
                                    <input type="number" step="0.0000001" min="-90" max="90" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror" placeholder="ex : 6.3653600" value="{{ old('latitude') }}" />
                                    @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="longitude" class="form-label">{{ __('Longitude') }}</label>
                                    <input type="number" step="0.0000001" min="-180" max="180" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror" placeholder="ex : 2.4183300" value="{{ old('longitude') }}" />
                                    @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">{{ __('Téléphone') }}</label>
                                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="ex : +22997000001" value="{{ old('phone') }}" maxlength="30" />
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="ex : entrepot@exemple.com" value="{{ old('email') }}" maxlength="150" />
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="notes" class="form-label">{{ __('Notes internes') }}</label>
                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" maxlength="{{ (int) getRequiredSettingValue('max_product_warehouse_notes_length', app()->getLocale()) }}">{{ old('notes') }}</textarea>
                                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                        </div>


                        {{-- Boutons navigation --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary d-none" id="btn-prev">
                                <i class="bx bx-arrow-back me-1"></i> {{ __('Précédent') }}
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="btn-next">
                                    {{ __('Suivant') }} <i class="bx bx-right-arrow-alt ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-success" id="btn-submit">
                                    <i class="bx bx-save me-1"></i> {{ __('Enregistrer') }}
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
    @include('v1.backend.layouts.partials._stepper-script', ['formId' => 'create-warehouse-form'])
    @include('v1.backend.layouts.partials._country-division-filter-script')
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
    .step-item.done .step-badge   { background: green; color: #fff; }
    .step-divider {
        flex: 1; height: 2px;
        background: #e9ecef;
        min-width: 40px;
    }
    .step-label { font-size: 13px; font-weight: 500; white-space: nowrap; }
</style>
@endpush


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
                    </div>

                    <form id="create-warehouse-form" action="{{ route('backend.product-warehouses.store') }}" method="POST">
                        @csrf

                        {{-- STEP 1 — Informations générales (non traduites) --}}
                        <div class="step-content" data-step-content="1">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">{{ __('Code') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="ex : COT-01" value="{{ old('code') }}" required />
                                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __("Code unique de l'entrepôt (ex : COT-01, PKO-01).") }}</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ __('Statut') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        @foreach (\App\Enums\Products\Warehouses\ProductWarehouseStatus::cases() as $status)
                                            <option value="{{ $status->value }}" {{ old('status', 'inactive') === $status->value ? 'selected' : '' }}>
                                                {{ $status->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                        </div>

                        {{-- STEP 2 — Traductions (une par langue) --}}
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
                                @forelse ($langs as $index => $lang)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="tab_lang_{{ $lang->code }}">

                                        <input type="hidden" name="translations[{{ $lang->code }}][lang_id]" value="{{ $lang->id }}" />

                                        <div class="mb-3">
                                            <label for="name_{{ $lang->code }}" class="form-label">{{ __('Nom') }} <span class="text-danger">*</span> <span class="text-muted">({{ $lang->name }})</span></label>
                                            <input type="text" name="translations[{{ $lang->code }}][name]" id="name_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.name') is-invalid @enderror" placeholder="ex : Cotonou — Entrepôt principal" value="{{ old('translations.' . $lang->code . '.name') }}" required />
                                            @error('translations.' . $lang->code . '.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small class="text-muted">{{ __('Le slug sera généré automatiquement.') }}</small>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="address_{{ $lang->code }}" class="form-label">{{ __('Adresse') }} <span class="text-muted">({{ $lang->name }})</span></label>
                                                <input type="text" name="translations[{{ $lang->code }}][address]" id="address_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.address') is-invalid @enderror" placeholder="{{ __('Adresse physique') }}" value="{{ old('translations.' . $lang->code . '.address') }}" />
                                                @error('translations.' . $lang->code . '.address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="city_{{ $lang->code }}" class="form-label">{{ __('Ville') }} <span class="text-muted">({{ $lang->name }})</span></label>
                                                <input type="text" name="translations[{{ $lang->code }}][city]" id="city_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.city') is-invalid @enderror" placeholder="ex : Cotonou" value="{{ old('translations.' . $lang->code . '.city') }}" />
                                                @error('translations.' . $lang->code . '.city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="phone_{{ $lang->code }}" class="form-label">{{ __('Téléphone') }} <span class="text-muted">({{ $lang->name }})</span></label>
                                                <input type="text" name="translations[{{ $lang->code }}][phone]" id="phone_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.phone') is-invalid @enderror" placeholder="ex : +229 21 00 00 01" value="{{ old('translations.' . $lang->code . '.phone') }}" />
                                                @error('translations.' . $lang->code . '.phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>

                                    </div>
                                @empty
                                    <div class="alert alert-warning mb-0">{{ __('Aucune langue active trouvée.') }}</div>
                                @endforelse
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
@endpush

@push('styles')
<style>
    .step-item { display: flex; align-items: center; gap: 8px; opacity: 0.4; }
    .step-item.active, .step-item.done { opacity: 1; }
    .step-badge {
        width: 30px; height: 30px; border-radius: 50%;
        background: #e9ecef; display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 13px;
    }
    .step-item.active .step-badge { background: #0c0c0c; color: #fff; }
    .step-item.done .step-badge { background: green; color: #fff; }
    .step-divider { flex: 1; height: 2px; background: #e9ecef; min-width: 40px; }
    .step-label { font-size: 13px; font-weight: 500; white-space: nowrap; }
</style>
@endpush

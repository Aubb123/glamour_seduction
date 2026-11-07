@extends('v1.backend.layouts.app')

@section('title')
    {{ __("Créer une valeur d'attribut de produits") }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __("Créer une valeur d'attribut de produits")])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __("Créer une valeur d'attribut de produits") }}</h5>
                    <a href="{{ route('backend.product-attribute-values.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">

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

                    <form id="create-value-form" action="{{ route('backend.product-attribute-values.store') }}" method="POST">
                        @csrf

                        {{-- STEP 1 --}}
                        <div class="step-content" data-step-content="1">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="attribute_id" class="form-label">{{ __("Attribut parent") }} <span class="text-danger">*</span></label>
                                    <select name="attribute_id" id="attribute_id" class="form-select @error('attribute_id') is-invalid @enderror" required>
                                        <option value="">{{ __('Sélectionner un attribut') }}</option>
                                        @foreach ($attributes as $attribute)
                                            <option value="{{ $attribute->id }}" data-type="{{ $attribute->type?->value ?? 'text' }}" {{ old('attribute_id') == $attribute->id ? 'selected' : '' }}>
                                                {{ $attribute->active_translation?->name ?? '#' . $attribute->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('attribute_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="value" class="form-label">{{ __("Clé machine") }} <span class="text-danger">*</span></label>
                                    <input type="text" name="value" id="value" class="form-control @error('value') is-invalid @enderror" placeholder="{{ __('ex : rouge, xl, silicone-medical') }}" value="{{ old('value') }}" pattern="[a-z0-9]+(-[a-z0-9]+)*" required />
                                    @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('Minuscules, sans espaces (ex : rouge, xl).') }}</small>
                                </div>

                                {{-- Code couleur HEX (affiché uniquement si l'attribut parent est de type "color") --}}
                                @include('v1.backend.pages.products.attribute-values.partials._hex-color-field')

                                <div class="col-md-6 mb-3">
                                    <label for="sort_order" class="form-label">{{ __("Ordre de tri") }} <span class="text-danger">*</span></label>
                                    <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" placeholder="0" value="{{ old('sort_order', 0) }}" min="0" required />
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ __('Statut') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', 'inactive') === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                        <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                        </div>

                        {{-- STEP 2 --}}
                        <div class="step-content d-none" data-step-content="2">

                            @include('v1.backend.pages.products.attribute-values.partials._form-translations', ['langs' => $langs])

                        </div>

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
    @include('v1.backend.layouts.partials._stepper-script', ['formId' => 'create-value-form'])
    @include('v1.backend.pages.products.attribute-values.partials._hex-color-script')
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

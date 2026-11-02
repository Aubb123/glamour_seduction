@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Créer un attribut de produit') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Créer un attribut de produit')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Créer un attribut de produit') }}</h5>
                    <a href="{{ route('backend.product-attributes.index') }}" class="btn btn-secondary btn-sm">
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

                    <form id="create-attribute-form" action="{{ route('backend.product-attributes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="step-content" data-step-content="1">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sort_order" class="form-label">{{ __('Ordre de tri') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" placeholder="0" value="{{ old('sort_order', 0) }}" min="0" required />
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('Ordre d\'affichage (croissant).') }}</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ __('Statut') }}</label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="active" {{ old('status', 'inactive') === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                        <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                        </div>

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
                                            <input type="text" name="translations[{{ $lang->code }}][name]" id="name_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.name') is-invalid @enderror" placeholder="{{ __('ex: Couleur...') }}" value="{{ old('translations.' . $lang->code . '.name') }}" required />
                                            @error('translations.' . $lang->code . '.name') <div class="invalid-feedback">{{ ucfirst($lang->name) }} : {{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="description_{{ $lang->code }}" class="form-label">{{ __('Description') }} <span class="text-muted">({{ $lang->name }})</span></label>
                                            <textarea name="translations[{{ $lang->code }}][description]" id="description_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.description') is-invalid @enderror" rows="5" placeholder="{{ __('Description de l\'attribut...') }}">{{ old('translations.' . $lang->code . '.description') }}</textarea>
                                            @error('translations.' . $lang->code . '.description') <div class="invalid-feedback">{{ ucfirst($lang->name) }} : {{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary d-none" id="btn-prev">
                                <i class="bx bx-arrow-back me-1"></i> {{ __('Précédent') }}
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="btn-next">
                                    {{ __('Suivant') }} <i class="bx bx-right-arrow-alt ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-success d-none" id="btn-submit">
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
    @include('v1.backend.layouts.partials._stepper-script', ['formId' => 'create-attribute-form'])
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

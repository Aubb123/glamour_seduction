@extends('v1.backend.layouts.app')

@section('title')
    {{ __("Modifier la valeur d'attribut") }} — {{ $value->active_translation?->label ?? '' }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __("Modifier la valeur d'attribut")])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __("Modifier la valeur d'attribut") }} — {{ $value->active_translation?->label ?? $value->value }}</h5>
                    <a href="{{ route('backend.product-attribute-values.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <form id="edit-value-form" action="{{ route('backend.product-attribute-values.update', ['productAttributeValue' => $value->uuid]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="attribute_id" class="form-label">{{ __("Attribut parent") }} <span class="text-danger">*</span></label>
                                <select name="attribute_id" id="attribute_id" class="form-select @error('attribute_id') is-invalid @enderror" required>
                                    <option value="">{{ __('— Sélectionner un attribut —') }}</option>
                                    @foreach ($attributes as $attribute)
                                        <option value="{{ $attribute->id }}" {{ old('attribute_id', $value->attribute_id) == $attribute->id ? 'selected' : '' }}>
                                            {{ $attribute->active_translation?->name ?? $attribute->uuid }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('attribute_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="value" class="form-label">{{ __("Clé machine") }} <span class="text-danger">*</span></label>
                                <input type="text" name="value" id="value" class="form-control @error('value') is-invalid @enderror" placeholder="{{ __('ex : rouge, xl, silicone-medical') }}" value="{{ old('value', $value->value) }}" pattern="[a-z0-9]+(-[a-z0-9]+)*" required />
                                @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">{{ __('Minuscules, sans espaces (ex : rouge, xl).') }}</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sort_order" class="form-label">{{ __("Ordre de tri") }} <span class="text-danger">*</span></label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" placeholder="0" value="{{ old('sort_order', $value->sort_order) }}" min="0" required />
                                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ __('Statut') }}</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $value->status->value) === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                    <option value="inactive" {{ old('status', $value->status->value) === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3">{{ __('Traductions') }}</h6>

                        <ul class="nav nav-tabs mb-3">
                            @foreach ($langs as $index => $lang)
                                <li class="nav-item">
                                    <a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#lang-{{ $lang->id }}">
                                        {{ strtoupper($lang->code) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @include('v1.backend.pages.products.attribute-values.partials._form-translations', ['langs' => $langs, 'value' => $value])
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
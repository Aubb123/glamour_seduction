@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Créer un emplacement d\'entrepôt') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Créer un emplacement d\'entrepôt')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Créer un emplacement d\'entrepôt') }}</h5>
                    <a href="{{ route('backend.product-warehouse-locations.index') }}" class="btn btn-secondary btn-sm">
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
                            <span class="step-label">{{ __('Notes internes') }}</span>
                        </div>
                    </div>

                    <form id="create-location-form" action="{{ route('backend.product-warehouse-locations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- STEP 1 --}}
                        <div class="step-content" data-step-content="1">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="warehouse_id" class="form-label">{{ __('Entrepôt') }} <span class="text-danger">*</span></label>
                                    <select name="warehouse_id" id="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                        <option value="">{{ __('Sélectionner un entrepôt') }}</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ (string) old('warehouse_id') === (string) $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->active_translation?->name ?? $warehouse->code }} ({{ $warehouse->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="parent_id" class="form-label">{{ __('Emplacement parent') }}</label>
                                    <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                        <option value="">{{ __('Aucun (emplacement racine)') }}</option>
                                        @foreach ($parentLocations as $parentLocation)
                                            <option value="{{ $parentLocation->id }}"
                                                    data-warehouse-id="{{ $parentLocation->warehouse_id }}"
                                                    {{ (string) old('parent_id') === (string) $parentLocation->id ? 'selected' : '' }}>
                                                {{ $parentLocation->code }} — {{ $parentLocation->active_translation?->name ?? __('Sans nom') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('Le parent doit appartenir au même entrepôt.') }}</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">{{ __('Type') }} <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                        @foreach (\App\Enums\Products\WarehouseLocations\ProductWarehouseLocationType::cases() as $type)
                                            <option value="{{ $type->value }}" {{ old('type', 'shelf') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">{{ __('Code') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="ex : COT-01-Z1" value="{{ old('code') }}" maxlength="30" required />
                                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('Majuscules, chiffres, tirets et underscores. Unique dans l\'entrepôt et immuable après création.') }}</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="sort_order" class="form-label">{{ __('Ordre de tri') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" placeholder="0" value="{{ old('sort_order', $lastSortOrder > 0 ? $lastSortOrder + 1 : 0) }}" min="0" required />
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div id="last-sort-order-wrapper">
                                        @include('v1.backend.layouts.partials._last-sort-order')
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ __('Statut') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                        <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                            <input type="text" name="translations[{{ $lang->code }}][name]" id="name_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.name') is-invalid @enderror" placeholder="ex : Étagère 1" value="{{ old('translations.' . $lang->code . '.name') }}" maxlength="150" required />
                                            @error('translations.' . $lang->code . '.name') <div class="invalid-feedback">{{ ucfirst($lang->name) }} : {{ $message }}</div> @enderror
                                            <small class="text-muted">{{ __('Le nom doit être unique dans cet entrepôt pour cette langue.') }}</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description_{{ $lang->code }}" class="form-label">{{ __('Description') }} <span class="text-danger">*</span> <span class="text-muted">({{ $lang->name }})</span></label>
                                            <textarea name="translations[{{ $lang->code }}][description]" id="description_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.description') is-invalid @enderror" rows="4" placeholder="{{ __('Description de cet emplacement...') }}" required>{{ old('translations.' . $lang->code . '.description') }}</textarea>
                                            @error('translations.' . $lang->code . '.description') <div class="invalid-feedback">{{ ucfirst($lang->name) }} : {{ $message }}</div> @enderror
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                        </div>

                        {{-- STEP 3 --}}
                        <div class="step-content d-none" data-step-content="3">

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="notes" class="form-label">{{ __('Notes internes') }}</label>
                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" maxlength="{{ (int) getRequiredSettingValue('max_product_warehouse_notes_length', app()->getLocale()) }}">{{ old('notes') }}</textarea>
                                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">{{ __('Notes libres, non traduites, réservées au back-office.') }}</small>
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
    @include('v1.backend.layouts.partials._stepper-script', ['formId' => 'create-location-form'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const warehouseSelect = document.getElementById('warehouse_id');
            const parentSelect = document.getElementById('parent_id');

            if (!warehouseSelect) {
                return;
            }

            function applyWarehouse() {
                const warehouseId = warehouseSelect.value;

                // Parent : ne garder que les emplacements du même entrepôt
                if (parentSelect) {
                    Array.from(parentSelect.options).forEach(function (option) {
                        if (!option.value) {
                            return;
                        }

                        const sameWarehouse = String(option.dataset.warehouseId) === String(warehouseId);
                        option.hidden = !sameWarehouse;
                        option.disabled = !sameWarehouse;

                        if (!sameWarehouse && option.selected) {
                            option.selected = false;
                        }
                    });
                }
            }

            warehouseSelect.addEventListener('change', applyWarehouse);
            applyWarehouse();
        });
    </script>
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

@push('styles')
    <style>
        .setting-card .card-header {
            cursor: pointer;
            user-select: none;
        }
        .setting-card .card-header:hover {
            background-color: #f1f5f9;
        }
        .setting-card .card-header .collapse-icon {
            transition: transform 0.2s ease;
        }
        .setting-card .card-header.collapsed .collapse-icon {
            transform: rotate(-90deg);
        }
    </style>
@endpush

@extends('v1.backend.layouts.app')

@section('title') {{ __('Paramètres') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Paramètres')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">{{ __('Paramètres du site') }}</h5>
                </div>

                <div class="card-body">

                    <form action="{{ route('backend.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        @forelse ($groupedSettings as $group => $settings)
                            <div class="card border mb-3 setting-card">
                                <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between collapsed" data-bs-toggle="collapse" data-bs-target="#collapse_{{ Str::slug($group) }}" aria-expanded="false">
                                    <h6 class="mb-0 fw-bold text-uppercase" style="font-size:0.85rem;">
                                        <i class="bx bx-cog me-2 text-primary"></i>{{ __(ucfirst($group)) }}
                                    </h6>
                                    <i class="bx bx-chevron-down collapse-icon"></i>
                                </div>

                                <div class="collapse" id="collapse_{{ Str::slug($group) }}">
                                    <div class="card-body">
                                         @foreach ($settings as $setting)
                                            <input type="hidden" name="{{ $setting->form_key }}[uuid]" value="{{ $setting->uuid }}">

                                            @php $isEditable = $setting->is_editable; @endphp

                                            <div class="row mb-3 align-items-start">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold mb-1">
                                                        {{ $setting->active_translation?->name ?: $setting->key }}
                                                    </label>
                                                    <br>
                                                    @if($setting->is_translatable)
                                                        <span class="badge bg-soft-info mb-1">{{ __('Multilingue') }}</span>
                                                    @endif
                                                    @if($setting->is_public)
                                                        <span class="badge bg-soft-success">{{ __('Public') }}</span>
                                                    @endif
                                                    @if(!$isEditable)
                                                        <span class="badge bg-soft-secondary">{{ __('Non modifiable') }}</span>
                                                    @endif
                                                    <p class="text-muted small mt-1 mb-0">
                                                        {{ $setting->active_translation?->description ?: __('Aucune description disponible.') }}
                                                    </p>
                                                </div>

                                                <div class="col-md-6">
                                                    @if($setting->is_translatable && $setting->type === 'string')
                                                        {{-- STRING TRADUISIBLE : onglets de langue pour modifier chaque valeur --}}
                                                        <ul class="nav nav-pills mb-2" style="gap:4px;">
                                                            @foreach ($langs as $index => $lang)
                                                                <li class="nav-item">
                                                                    <a class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                                                       style="padding:2px 8px;font-size:0.75rem;"
                                                                       data-bs-toggle="pill"
                                                                       href="#setting_{{ $setting->uuid }}_{{ $lang->code }}">
                                                                        {{ $lang->name }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>

                                                        <div class="tab-content">
                                                            @foreach ($langs as $index => $lang)
                                                                @php
                                                                    $translation = $setting->getTranslationForLang($lang->id);
                                                                    $transKey = $setting->getTranslationFormKey($lang->id);
                                                                    $translationValue = $translation?->value !== null && $translation?->value !== ''
                                                                        ? $translation->value
                                                                        : $setting->value;
                                                                @endphp
                                                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                                                     id="setting_{{ $setting->uuid }}_{{ $lang->code }}">
                                                                    <input type="text" name="{{ $transKey }}[value]" class="form-control form-control-sm" value="{{ $translationValue ?? '' }}" {{ !$isEditable ? 'disabled' : '' }}>
                                                                    <input type="hidden" name="{{ $transKey }}[lang_id]" value="{{ $lang->id }}">
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                    @elseif($setting->is_translatable)
                                                        {{-- BOOLEAN / INTEGER TRADUISIBLE : valeur partagée (pas d'onglets) --}}
                                                        @if($setting->type === 'boolean')
                                                            <select name="{{ $setting->form_key }}[value]" class="form-select form-select-sm" {{ !$isEditable ? 'disabled' : '' }}>
                                                                <option value="1" {{ $setting->getValue() ? 'selected' : '' }}>{{ __('Oui') }}</option>
                                                                <option value="0" {{ !$setting->getValue() ? 'selected' : '' }}>{{ __('Non') }}</option>
                                                            </select>
                                                        @else
                                                            <input type="number" name="{{ $setting->form_key }}[value]" class="form-control form-control-sm" value="{{ $setting->getValue() ?? '' }}" {{ !$isEditable ? 'disabled' : '' }}>
                                                        @endif

                                                    @else
                                                        {{-- : selon le type --}}
                                                        @if($setting->type === 'boolean')
                                                            <select name="{{ $setting->form_key }}[value]" class="form-select form-select-sm" {{ !$isEditable ? 'disabled' : '' }}>
                                                                <option value="1" {{ $setting->getValue() ? 'selected' : '' }}>{{ __('Oui') }}</option>
                                                                <option value="0" {{ !$setting->getValue() ? 'selected' : '' }}>{{ __('Non') }}</option>
                                                            </select>
                                                        @elseif($setting->type === 'integer')
                                                            <input type="number" name="{{ $setting->form_key }}[value]" class="form-control form-control-sm" value="{{ $setting->getValue() ?? '' }}" {{ !$isEditable ? 'disabled' : '' }}>
                                                        @else
                                                            <input type="text" name="{{ $setting->form_key }}[value]" class="form-control form-control-sm" value="{{ $setting->getValue() ?? '' }}" {{ !$isEditable ? 'disabled' : '' }}>
                                                        @endif
                                                    @endif

                                                    <div class="form-check mt-2">
                                                        <input type="hidden" name="{{ $setting->form_key }}[is_public]" value="0">
                                                        <input type="checkbox" name="{{ $setting->form_key }}[is_public]" class="form-check-input" value="1" id="public_{{ $setting->uuid }}" {{ $setting->is_public ? 'checked' : '' }} {{ !$isEditable ? 'disabled' : '' }}>
                                                        <label class="form-check-label" for="public_{{ $setting->uuid }}" style="font-size:0.8rem;">
                                                            {{ __('Accessible publiquement') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(!$loop->last)
                                                <hr class="my-3">
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                <p>{{ __('Aucun paramètre trouvé.') }}</p>
                            </div>
                        @endforelse

                        @if($groupedSettings->isNotEmpty())
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> {{ __('Enregistrer les modifications') }}
                                </button>
                            </div>
                        @endif

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Initialiser les collapse pour que le premier groupe soit ouvert par défaut
        document.addEventListener('DOMContentLoaded', function() {
            const firstCollapse = document.querySelector('.collapse');
            if (firstCollapse) {
                firstCollapse.classList.add('show');
                const header = firstCollapse.closest('.setting-card').querySelector('.card-header');
                header.setAttribute('aria-expanded', 'true');
                header.classList.remove('collapsed');
            }
        });

        // Basculer l'icône chevron
        document.querySelectorAll('.setting-card .card-header').forEach(header => {
            header.addEventListener('click', function() {
                const icon = this.querySelector('.collapse-icon');
                if (this.classList.contains('collapsed')) {
                    icon.style.transform = 'rotate(-90deg)';
                } else {
                    icon.style.transform = 'rotate(0deg)';
                }
            });
        });
    </script>
@endpush
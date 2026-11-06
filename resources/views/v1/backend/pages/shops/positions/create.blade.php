@extends('v1.backend.layouts.app')

@section('title') Créer un poste @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Créer un poste'])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Créer un poste</h5>
                    <a href="{{ route('backend.positions.index') }}" class="btn btn-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i> Retour</a>
                </div>
                <div class="card-body">
                    <form id="create-position-form" action="{{ route('backend.positions.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="ex : SHOP-MANAGER" value="{{ old('code') }}" required />
                                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="context" class="form-label">Portée <span class="text-danger">*</span></label>
                                <select name="context" id="context" class="form-select @error('context') is-invalid @enderror">
                                    <option value="shop" {{ old('context', 'both') === 'shop' ? 'selected' : '' }}>Boutique</option>
                                    <option value="warehouse" {{ old('context', 'both') === 'warehouse' ? 'selected' : '' }}>Entrepôt</option>
                                    <option value="both" {{ old('context', 'both') === 'both' ? 'selected' : '' }}>Boutique et entrepôt</option>
                                </select>
                                @error('context') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Statut</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', 'inactive') === 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sort_order" class="form-label">Ordre de tri <span class="text-danger">*</span></label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 0) }}" min="0" required />
                                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3"><i class="bx bx-globe me-2"></i>Traductions</h6>

                        <ul class="nav nav-tabs mb-3">
                            @foreach ($langs as $index => $lang)
                                <li class="nav-item"><a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#tab_lang_{{ $lang->code }}">{{ $lang->name }}</a></li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach ($langs as $index => $lang)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="tab_lang_{{ $lang->code }}">
                                    <input type="hidden" name="translations[{{ $lang->code }}][lang_id]" value="{{ $lang->id }}" />
                                    <div class="mb-3">
                                        <label for="name_{{ $lang->code }}" class="form-label">Nom <span class="text-danger">*</span> <span class="text-muted">({{ $lang->name }})</span></label>
                                        <input type="text" name="translations[{{ $lang->code }}][name]" id="name_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.name') is-invalid @enderror" placeholder="ex : Responsable de boutique" value="{{ old('translations.' . $lang->code . '.name') }}" required />
                                        @error('translations.' . $lang->code . '.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="description_{{ $lang->code }}" class="form-label">Description <span class="text-muted">({{ $lang->name }})</span></label>
                                        <textarea name="translations[{{ $lang->code }}][description]" id="description_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.description') is-invalid @enderror" rows="3">{{ old('translations.' . $lang->code . '.description') }}</textarea>
                                        @error('translations.' . $lang->code . '.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('backend.positions.index') }}" class="btn btn-secondary"><i class="bx bx-x me-1"></i> Annuler</a>
                            <button type="submit" class="btn btn-success"><i class="bx bx-save me-1"></i> Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

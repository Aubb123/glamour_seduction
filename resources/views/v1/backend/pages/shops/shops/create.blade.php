@extends('v1.backend.layouts.app')

@section('title')
    Créer une boutique
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Créer une boutique'])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Créer une boutique</h5>
                    <a href="{{ route('backend.shops.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Retour
                    </a>
                </div>

                <div class="card-body">

                    <form id="create-shop-form" action="{{ route('backend.shops.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="ex : COT-SHOP-01" value="{{ old('code') }}" required />
                                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sort_order" class="form-label">Ordre de tri <span class="text-danger">*</span></label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" placeholder="0" value="{{ old('sort_order', 0) }}" min="0" required />
                                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="boutique@exemple.com" value="{{ old('email') }}" />
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="ex : +229 21 00 00 01" value="{{ old('phone') }}" />
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Adresse</label>
                                <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Adresse physique" value="{{ old('address') }}" />
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">Ville</label>
                                <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" placeholder="ex : Cotonou" value="{{ old('city') }}" />
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Statut</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', 'inactive') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">Boutique par défaut</label>
                                </div>
                                <small class="text-muted">Une seule boutique par défaut.</small>
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3"><i class="bx bx-building-house me-2"></i>Entrepôts rattachés <span class="text-danger">*</span></h6>
                        @error('warehouses') <div class="text-danger mb-2">{{ $message }}</div> @enderror
                        <small class="text-muted d-block mb-3">Une boutique doit être rattachée à au moins un entrepôt. Cochez l'entrepôt principal d'approvisionnement.</small>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Rattacher</th>
                                        <th>Principal</th>
                                        <th>Code</th>
                                        <th>Nom</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($warehouses as $warehouse)
                                        <tr>
                                            <td>
                                                <input class="form-check-input" type="checkbox" name="warehouses[]" value="{{ $warehouse->uuid }}" {{ in_array($warehouse->uuid, old('warehouses', []), true) ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <input class="form-check-input" type="radio" name="primary_warehouse_uuid" value="{{ $warehouse->uuid }}" {{ old('primary_warehouse_uuid') === $warehouse->uuid ? 'checked' : '' }}>
                                            </td>
                                            <td><code>{{ $warehouse->code }}</code></td>
                                            <td>{{ $warehouse->name }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">Aucun entrepôt actif disponible.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <h6 class="mb-3"><i class="bx bx-globe me-2"></i>Traductions</h6>

                        <ul class="nav nav-tabs mb-3">
                            @foreach ($langs as $index => $lang)
                                <li class="nav-item">
                                    <a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#tab_lang_{{ $lang->code }}">{{ $lang->name }}</a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach ($langs as $index => $lang)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="tab_lang_{{ $lang->code }}">

                                    <input type="hidden" name="translations[{{ $lang->code }}][lang_id]" value="{{ $lang->id }}" />

                                    <div class="mb-3">
                                        <label for="name_{{ $lang->code }}" class="form-label">Nom <span class="text-danger">*</span> <span class="text-muted">({{ $lang->name }})</span></label>
                                        <input type="text" name="translations[{{ $lang->code }}][name]" id="name_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.name') is-invalid @enderror" placeholder="ex : Glamour Cotonou" value="{{ old('translations.' . $lang->code . '.name') }}" required />
                                        @error('translations.' . $lang->code . '.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        <small class="text-muted">Le slug sera généré automatiquement.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description_{{ $lang->code }}" class="form-label">Description <span class="text-muted">({{ $lang->name }})</span></label>
                                        <textarea name="translations[{{ $lang->code }}][description]" id="description_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.description') is-invalid @enderror" rows="3" placeholder="Description de cette boutique...">{{ old('translations.' . $lang->code . '.description') }}</textarea>
                                        @error('translations.' . $lang->code . '.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('backend.shops.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save me-1"></i> Enregistrer
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

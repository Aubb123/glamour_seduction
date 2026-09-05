@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Modifier un article') }} — <code>{{ $post->active_translation?->title ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.partials._summernote_editor', ['editMode' => true])

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Modifier un article')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('Modifier') }} — <code>{{ $post->active_translation?->title ?? '' }}</code>
                    </h5>
                    <a href="{{ route('backend.blog-posts.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">

                    {{-- Stepper Nav --}}
                    <div class="d-flex align-items-center mb-4 gap-3">
                        <div class="step-item" data-step="1">
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
                            <span class="step-label">{{ __('Multimédias') }}</span>
                        </div>
                    </div>

                    <form id="edit-post-form"
                          action="{{ route('backend.blog-posts.update', ['blogPost' => $post->uuid]) }}"
                          enctype="multipart/form-data"
                          method="POST">
                        @csrf
                        @method('PUT')

                        {{-- STEP 1 --}}
                        <div class="step-content" data-step-content="1">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">{{ __('Catégorie') }} <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                        <option value="">{{ __('Sélectionner une catégorie') }}</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id) == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->active_translation?->name ?? '#' . $cat->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="sort_order" class="form-label">{{ __('Ordre de tri') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $post->sort_order) }}" min="0" required />
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="tags" class="form-label">{{ __('Tags') }} <span class="text-danger">*</span></label>
                                    <select name="tags[]" id="tags" class="form-select @error('tags') is-invalid @enderror" multiple size="6" required>
                                        @foreach ($tags as $tag)
                                            <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $tag->active_translation?->name ?? '#' . $tag->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ __('Statut') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="draft" {{ old('status', $post->status?->value) === 'draft' ? 'selected' : '' }}>{{ __('Brouillon') }}</option>
                                        <option value="published" {{ old('status', $post->status?->value) === 'published' ? 'selected' : '' }}>{{ __('Publié') }}</option>
                                        <option value="archived" {{ old('status', $post->status?->value) === 'archived' ? 'selected' : '' }}>{{ __('Archivé') }}</option>
                                        <option value="scheduled" {{ old('status', $post->status?->value) === 'scheduled' ? 'selected' : '' }}>{{ __('Programmé') }}</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3" id="published-at-wrapper" style="{{ $post->status?->value === 'scheduled' ? '' : ($post->status?->value === 'archived' ? '' : 'display: none;') }}">
                                    <label for="published_at" class="form-label">{{ __('Date de publication') }} <span class="text-danger" id="published-at-required">{{ $post->status?->value === 'archived' ? '(*)' : '*' }}</span></label>
                                    @php
                                        $publishedAtValue = old('published_at', ($post->published_at ?? getDateTime())->format('Y-m-d\TH:i'));
                                        $isArchived = $post->status?->value === 'archived';
                                    @endphp
                                    <input type="datetime-local" name="published_at" id="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ $publishedAtValue }}" {{ $isArchived ? 'disabled' : '' }} />
                                    <small class="text-muted">
                                        @if($post->status?->value === 'archived')
                                            {{ __('Date de publication conservée (non modifiable).') }}
                                        @else
                                            {{ __('Requis pour le statut "Programmé".') }}
                                        @endif
                                    </small>
                                    @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                            @error('translations.' . $lang->code . '.title')
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
                                            <label for="title_{{ $lang->code }}" class="form-label">
                                                {{ __('Titre') }} <span class="text-danger">*</span>
                                                <span class="text-muted">({{ $lang->name }})</span>
                                            </label>
                                            <input type="text" name="translations[{{ $lang->code }}][title]" id="title_{{ $lang->code }}" class="form-control @error('translations.' . $lang->code . '.title') is-invalid @enderror" value="{{ old('translations.' . $lang->code . '.title', getTranslation($post, $lang->id)?->title ?? '') }}" required />
                                            @error('translations.' . $lang->code . '.title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">{{ __('Le slug sera généré automatiquement.') }}</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="summernote-{{ $lang->code }}" class="form-label">
                                                {{ __('Contenu') }} <span class="text-danger">*</span>
                                                <span class="text-muted">({{ $lang->name }})</span>
                                            </label>
                                            <textarea name="translations[{{ $lang->code }}][content]" class="form-control @error('translations.' . $lang->code . '.content') is-invalid @enderror" rows="10" placeholder="{{ __('Contenu de l\'article...') }}" id="summernote-{{ $lang->code }}" required>
                                                {!! old('translations.' . $lang->code . '.content') ?? getTranslation($post, $lang->id)?->content ?? '' !!}
                                            </textarea>
                                            @error('translations.' . $lang->code . '.content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>

                        {{-- STEP 3 --}}
                        <div class="step-content d-none" data-step-content="3">

                            @include('v1.backend.layouts.partials._image-upload-preview', [
                                'form_label' => __('Image principale de l\'article'),
                                'format_accept' => __('Formats acceptés : JPG, PNG, WEBP. Taille max : 2 Mo.'),
                                'entity' => $post ?? null,
                            ])

                        </div>

                        {{-- Boutons navigation --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary d-none" id="btn-prev">
                                <i class="bx bx-arrow-back me-1"></i> {{ __('Précédent') }}
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="btn-next">
                                    {{ __('Suivant') }} <i class="bx bx-arrow-right ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-success d-none" id="btn-submit">
                                    <i class="bx bx-save me-1"></i> {{ __('Mettre à jour') }}
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
    @include('v1.backend.layouts.partials._stepper-script', ['formId' => 'edit-post-form'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusSelect = document.getElementById('status');
            const publishedAtWrapper = document.getElementById('published-at-wrapper');
            const publishedAtInput = document.getElementById('published_at');

            function togglePublishedAtField() {
                const status = statusSelect.value;

                if (status === 'scheduled') {
                    publishedAtWrapper.style.display = '';
                    publishedAtInput.disabled = false;
                    publishedAtInput.required = true;
                } else if (status === 'archived') {
                    publishedAtWrapper.style.display = '';
                    publishedAtInput.disabled = true;
                    publishedAtInput.required = false;
                } else {
                    publishedAtWrapper.style.display = 'none';
                    publishedAtInput.disabled = false;
                    publishedAtInput.required = false;
                    publishedAtInput.value = '';
                }
            }

            statusSelect.addEventListener('change', togglePublishedAtField);
            togglePublishedAtField();
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

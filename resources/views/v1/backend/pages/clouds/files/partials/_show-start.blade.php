<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    {{ __('Fichier') }} — <code>{{ $file->file_name }}</code>
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.cloud-files.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>

                    <form action="{{ route('backend.cloud-files.destroy', ['cloudFile' => $file->uuid]) }}" method="POST" id="delete-form-{{ $file->uuid }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmAction('delete-form-{{ $file->uuid }}', '{{ __('Supprimer ce fichier ?') }}', '{{ __('Supprimer') }}', 'warning')">
                            <i class="bx bx-trash me-1"></i> {{ __('Supprimer') }}
                        </button>
                    </form>

                </div>
            </div>

            <div class="card-body">

                @include('v1.backend.pages.clouds.files.partials._show-navigation', ['file' => $file])
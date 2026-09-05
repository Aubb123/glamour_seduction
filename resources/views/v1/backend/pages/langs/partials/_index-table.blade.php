@push('styles')
    <style>
        .lang-table img { width: 24px; height: 24px; object-fit: cover; border-radius: 4px; }
    </style>
@endpush

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Langues') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap mb-0 lang-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('Drapeau') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Nom') }}</th>
                                <th>{{ __('Natif') }}</th>
                                <th>{{ __('Direction') }}</th>
                                <th>{{ __('Défaut') }}</th>
                                <th>{{ __('Actif') }}</th>
                                <th>{{ __('Ordre') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($langs as $lang)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($lang->file_path)
                                            <img src="{{ asset(getEnvFolder().$lang->file_path) }}" alt="{{ $lang->code }}">
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">{{ $lang->code }}</td>
                                    <td class="fw-bold">{{ $lang->name }}</td>
                                    <td>{{ $lang->native }}</td>
                                    <td>
                                        @if ($lang->direction === 'rtl')
                                            <span class="badge bg-soft-danger">{{ __('RTL') }}</span>
                                        @else
                                            <span class="badge bg-soft-info">{{ __('LTR') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($lang->is_default)
                                            <span class="badge bg-soft-primary">{{ __('Défaut') }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($lang->is_active)
                                            <span class="badge bg-soft-success">{{ __('Actif') }}</span>
                                        @else
                                            <span class="badge bg-soft-secondary">{{ __('Inactif') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $lang->sort_order }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @can('langs.deactivate')
                                                @if ($lang->is_active)
                                                    <form action="{{ route('backend.langs.deactivate', ['lang' => $lang->uuid]) }}" method="POST" style="display:inline-block" id="deactivate-form-{{ $lang->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-outline-warning" title="{{ __('Désactiver') }}"
                                                                onclick="confirmAction('deactivate-form-{{ $lang->uuid }}', '{{ __('Désactiver cette langue ?') }}', '{{ __('Désactiver') }}', 'warning')">
                                                            <i class="bx bx-x"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                            @can('langs.activate')
                                                @if (! $lang->is_active)
                                                    <form action="{{ route('backend.langs.activate', ['lang' => $lang->uuid]) }}" method="POST" style="display:inline-block" id="activate-form-{{ $lang->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-outline-success" title="{{ __('Activer') }}"
                                                                onclick="confirmAction('activate-form-{{ $lang->uuid }}', '{{ __('Activer cette langue ?') }}', '{{ __('Activer') }}', 'success')">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucune langue trouvée.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function confirmAction(formId, title, confirmText, icon) {
            Swal.fire({
                title: title,
                icon: icon,
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: '{{ __('Annuler') }}',
                confirmButtonColor: icon === 'success' ? '#198754' : '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
@endpush

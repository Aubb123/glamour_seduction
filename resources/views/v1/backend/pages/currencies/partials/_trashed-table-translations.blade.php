<div class="table-responsive">
    <table class="table table-centered text-nowrap" id="currency-translations-table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.langue') }}</th>
                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.nom') }}</th>
                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.slug') }}</th>
                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.description') }}</th>
                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.creeLe') }}</th>
                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.modifieLe') }}</th>
                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.supprimeLe') }}</th>
                <th scope="col">{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($currencyTranslations as $translation)
                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        <span class="badge badge-soft-primary">
                            {{ $translation->lang?->code ?? '—' }}
                        </span>
                    </td>

                    <td class="fw-bold">
                        {{ $translation->name ?? '—' }}
                    </td>

                    <td class="text-muted">
                        <code>{{ $translation->slug ?? '—' }}</code>
                    </td>

                    <td class="text-muted">
                        {{ Str::limit($translation->description ?? '—', 50) }}
                    </td>

                    <td class="text-muted">
                        @formatDate($translation->created_at, 'D MMMM YYYY - HH:mm:ss')
                    </td>

                    <td class="text-muted">
                        @formatDate($translation->updated_at, 'D MMMM YYYY - HH:mm:ss')
                    </td>

                    <td class="text-muted">
                        @formatDate($translation->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                    </td>

                    <td>
                        <div class="hstack gap-2 fs-15">

                            <form action="{{ route('backend.currencies.translation.restore', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $translation->uuid }}">
                                @csrf
                                @method('PATCH')
                                <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.restaurer') }}" onclick="confirmAction('restore-form-{{ $translation->uuid }}', '{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.restaurerCetteTraduction') }}', '{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.restaurer') }}', 'success')">
                                    <i class="bx bx-undo"></i>
                                </button>
                            </form>

                            <form action="{{ route('backend.currencies.translation.force_destroy', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $translation->uuid }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.supprimerDefinitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $translation->uuid }}', '{{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.confirmerSuppressionDefinitive') }}')">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                        {{ __('v1/backend/views/pages/currencies/partials/_trashed-table-translations.aucuneTraductionTrouvee') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#currency-translations-table').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>' +
                'tr'                                        +
                '<"row mt-3"<"col-md-5"l><"col-md-7"p>>',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: "{{ app()->getLocale() === 'fr' ? asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/fr-FR.json') : asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/en-GB.json') }}"
                },
            });
        });
    </script>
@endpush

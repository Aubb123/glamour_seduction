{{-- SweetAlert2 messages et confirmations du backend. --}}
<script>
    function confirmDelete(token, confirmationText) {
        const form = document.getElementById('delete-form-' + token);
        if (!form) return;

        Swal.fire({
            title: @js(app()->getLocale() === 'fr' ? 'Confirmation de suppression' : 'Delete confirmation'),
            text: confirmationText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: @js(app()->getLocale() === 'fr' ? 'Oui, supprimer' : 'Yes, delete'),
            cancelButtonText: @js(app()->getLocale() === 'fr' ? 'Annuler' : 'Cancel'),
        }).then((result) => {
            if (!result.isConfirmed) return;
            const button = form.querySelector('button');
            if (button) {
                button.disabled = true;
                button.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>${@js(app()->getLocale() === 'fr' ? 'Suppression…' : 'Deleting…')}`;
            }
            form.submit();
        });
    }

    function confirmAction(formId, confirmationText, confirmButtonText = 'Confirmer', icon = 'warning') {
        const form = document.getElementById(formId);
        if (!form) return;

        Swal.fire({
            title: @js(app()->getLocale() === 'fr' ? 'Confirmation' : 'Confirmation'),
            text: confirmationText,
            icon,
            showCancelButton: true,
            confirmButtonColor: icon === 'danger' || icon === 'warning' ? '#d33' : '#198754',
            cancelButtonColor: '#3085d6',
            confirmButtonText,
            cancelButtonText: @js(app()->getLocale() === 'fr' ? 'Annuler' : 'Cancel'),
        }).then((result) => {
            if (!result.isConfirmed) return;
            const button = form.querySelector('button');
            if (button) {
                button.disabled = true;
                button.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>${@js(app()->getLocale() === 'fr' ? 'Traitement…' : 'Processing…')}`;
            }
            form.submit();
        });
    }
</script>

<style>
    .swal2-toast { padding: 10px 15px !important; align-items: center !important; }
    .swal2-toast .swal2-icon { margin-top: 0 !important; margin-right: 10px !important; }
    .swal2-toast .swal2-title { display: flex !important; align-items: center !important; line-height: normal !important; margin: 0 !important; }
    .swal2-toast .swal2-html-container { margin: 0 !important; padding: 0 !important; }
</style>

@if ($errors->any())
    <script>
        (() => {
            const messages = @json($errors->all());
            const title = @js(app()->getLocale() === 'fr' ? 'Erreur de validation' : 'Validation error');
            const confirmText = @js(app()->getLocale() === 'fr' ? 'Corriger' : 'Fix errors');
            Swal.fire({
                title,
                html: messages.length > 1 ? '• ' + messages.join('<br>• ') : messages[0],
                icon: 'error',
                confirmButtonText: confirmText,
                confirmButtonColor: '#dc3545',
                customClass: { popup: 'animate__animated animate__shakeX' },
            });
        })();
    </script>
@endif

@foreach (['success' => 'success', 'info' => 'info', 'warning' => 'warning', 'error' => 'error'] as $flashKey => $icon)
    @if ($message = Session::get($flashKey))
        <script>
            (() => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    showCloseButton: true,
                    timer: 30000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    },
                });
                Toast.fire({ icon: @js($icon), title: @js($message) });
            })();
        </script>
    @endif
@endforeach

@if ($message = Session::get('success_auth'))
    <script>
        (() => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                showCloseButton: true,
                timer: 20000,
                timerProgressBar: true,
            });
            Toast.fire({ icon: 'success', title: @js($message) });
        })();
    </script>
@endif

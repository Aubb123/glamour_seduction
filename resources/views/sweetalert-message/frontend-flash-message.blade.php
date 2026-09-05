{{-- 
    @if ($errors->any())
        <script>
            // Construction du message d'erreur détaillé
            let errorMessages = [];
            @foreach ($errors->all() as $error)
                errorMessages.push("{!! addslashes($error) !!}");
            @endforeach
            
            let message = errorMessages.length > 1 
                ? "Erreurs détectées :<br>• " + errorMessages.join("<br>• ")
                : errorMessages[0];

            Swal.fire({
                title: '<span style="color: #dc3545;">Erreur de validation</span>',
                html: '<span style="color: #333;">' + message + '</span>',
                icon: 'error',
                confirmButtonText: 'Corriger',
                confirmButtonColor: '#dc3545',
                customClass: {
                    popup: 'animate__animated animate__shakeX'
                }
            });
        </script>
    @endif

    @if ($message = Session::get('success'))
        <script>
            let successMessage = "{!! addslashes($message) !!}";

            Swal.fire({
                title: '<span style="color: #198754;">Succès !</span>',
                html: '<span style="color: #333;">' + successMessage + '</span>',
                icon: 'success',
                confirmButtonText: 'Parfait !',
                confirmButtonColor: '#198754',
                timer: 20000,
                timerProgressBar: true,
                showCloseButton: true,
                customClass: {
                    popup: 'animate__animated animate__fadeInDown'
                }
            });
        </script>
    @endif

    @if ($message = Session::get('info'))
        <script>
            let infoMessage = "{!! addslashes($message) !!}";

            Swal.fire({
                title: '<span style="color: #0dcaf0;">Information</span>',
                html: '<span style="color: #333;">' + infoMessage + '</span>',
                icon: 'info',
                confirmButtonText: 'Compris',
                confirmButtonColor: '#0dcaf0',
                customClass: {
                    popup: 'animate__animated animate__fadeInDown'
                }
            });
        </script>
    @endif

    @if ($message = Session::get('warning'))
        <script>
            let warningMessage = "{!! addslashes($message) !!}";

            Swal.fire({
                title: '<span style="color: #ffc107;">Attention !</span>',
                html: '<span style="color: #333;">' + warningMessage + '</span>',
                icon: 'warning',
                confirmButtonText: 'J\'ai compris',
                confirmButtonColor: '#ffc107',
                customClass: {
                    popup: 'animate__animated animate__pulse'
                }
            });
        </script>
    @endif

    @if ($message = Session::get('error'))
        <script>
            let errorMessage = "{!! addslashes($message) !!}";

            Swal.fire({
                title: '<span style="color: #dc3545;">Erreur</span>',
                html: '<span style="color: #333;">' + errorMessage + '</span>',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545',
                customClass: {
                    popup: 'animate__animated animate__shakeX'
                }
            });
        </script>
    @endif

    @if ($message = Session::get('success_auth'))
        <script>
            let successMessage = "{!! addslashes($message) !!}";

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 20000, // Réduit de 12000 à 20000ms
                timerProgressBar: true,
                showCloseButton: true,
                customClass: {
                    popup: 'colored-toast'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            Toast.fire({
                icon: 'success',
                title: successMessage,
                showClass: {
                    popup: 'animate__animated animate__fadeInRight'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutRight'
                }
            });
        </script>
    @endif

    <script>

        function confirmDelete(user, textDeConfirmation) {
            // Utilisation de SweetAlert2 pour afficher la confirmation avec le texte personnalisé
            Swal.fire({
                title: 'Confirmation de suppression',
                text: textDeConfirmation,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si l'utilisateur confirme la suppression, soumettez le formulaire
                    document.getElementById('delete-form-' + user).submit();
                }
            });
        }

    </script>

    <style>
        .colored-toast.swal2-icon-success {
            background-color: #a5dc86 !important;
        }
        
        .colored-toast.swal2-icon-error {
            background-color: #f27474 !important;
        }
        
        .colored-toast.swal2-icon-warning {
            background-color: #f8bb86 !important;
        }
        
        .colored-toast.swal2-icon-info {
            background-color: #3fc3ee !important;
        }
    </style>
--}}
<!-- ------------------------------------------------------------------------------------------------------------------------------- -->

<!-- ------------------------------------------------------------------------------------------------------------------------------- -->
<style>
    /* Alignement correct du texte et de l'icône dans les toasts SweetAlert */
    .swal2-toast {
        padding: 10px 15px !important;
        align-items: center !important;
    }

    .swal2-toast .swal2-icon {
        margin-top: 0 !important;
        margin-right: 10px !important;
    }

    .swal2-toast .swal2-title {
        display: flex !important;
        align-items: center !important;
        line-height: normal !important;
        margin: 0 !important;
    }

    .swal2-toast .swal2-html-container {
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Personnalisation du bouton de fermeture 'X' */
    .swal2-toast .swal2-close {
        color: #dc3545 !important; /* Couleur rouge */
        font-size: 22px !important;
        font-weight: bold !important;
        transition: all 0.3s ease !important;
        opacity: 0.8 !important;
    }

    /* Effet hover sur le bouton 'X' */
    .swal2-toast .swal2-close:hover {
        color: #c82333 !important; /* Rouge plus foncé au survol */
        transform: scale(1.2) rotate(90deg) !important; /* Agrandit + rotation */
        opacity: 1 !important;
    }

    /* Style pour les listes d'erreurs dans le toast */
    .swal2-toast .swal2-html-container ul {
        text-align: left !important;
        margin: 5px 0 !important;
        padding-left: 20px !important;
    }
</style>

@if ($errors->any())
    <script>
        // Construction du message d'erreur détaillé
        let errorMessages = [];
        @foreach ($errors->all() as $error)
            errorMessages.push("{!! addslashes($error) !!}");
        @endforeach
        
        let message = errorMessages.length > 1 
            ? "Erreurs détectées :<br>• " + errorMessages.join("<br>• ")
            : errorMessages[0];

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            showCloseButton: true,
            timer: 30000, // 30 secondes pour avoir le temps de lire les erreurs
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        
        Toast.fire({
            icon: "error",
            html: message // Utilise 'html' au lieu de 'title' pour afficher le HTML
        });
    </script>
@endif

@if ($message = Session::get('success'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            showCloseButton: true,
            timer: 30000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        
        Toast.fire({
            icon: "success",
            title: "{!! addslashes($message) !!}"
        });
    </script>
@endif

@if ($message = Session::get('info'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            showCloseButton: true,
            timer: 30000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        
        Toast.fire({
            icon: "info",
            title: "{!! addslashes($message) !!}"
        });
    </script>
@endif

@if ($message = Session::get('warning'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            showCloseButton: true,
            timer: 30000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        
        Toast.fire({
            icon: "warning",
            title: "{!! addslashes($message) !!}"
        });
    </script>
@endif

@if ($message = Session::get('error'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            showCloseButton: true,
            timer: 30000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        
        Toast.fire({
            icon: "error",
            title: "{!! addslashes($message) !!}"
        });
    </script>
@endif

<script>

    function confirmDelete(token, textDeConfirmation) {
        // Utilisation de SweetAlert2 pour afficher la confirmation avec le texte personnalisé
        Swal.fire({
            title: 'Confirmation de suppression',
            text: textDeConfirmation,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
              if (result.isConfirmed) {

                const form = document.getElementById('delete-form-' + token);
                const button = form.querySelector("button");

                // Désactiver le bouton
                button.disabled = true;

                // Remplacer par loader
                button.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Suppression...
                `;

                // Empêcher double clic
                form.classList.add('submitting');

                // Soumettre
                form.submit();
            }
        });
    }

</script>



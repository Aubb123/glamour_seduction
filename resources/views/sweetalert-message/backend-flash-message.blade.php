{{-- 
    Gestion des alertes avec SweetAlert2
    Types d'icônes disponibles: success, error, warning, info, question


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
--}}
    <script>

        function confirmDelete(token, textDeConfirmation) {
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

                    // // Désactiver le bouton
                    // button.disabled = true;
                    // // Remplacer par loader
                    // // button.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Suppression...`;
                    // // button.innerHTML = `<span class="spinner-border spinner-border-sm"></span>Suppression...`;
                    // button.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;

                    button.disabled = true;
                    button.classList.remove('btn-icon');
                    button.style.width = 'auto';
                    button.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Suppression...
                    `;

                    // Empêcher double clic
                    form.classList.add('submitting');

                    // Soumettre
                    form.submit();
                }
            });
        }

        function confirmAction(formId, textDeConfirmation, confirmButtonText = 'Confirmer', icon = 'warning') {
            Swal.fire({
                title: 'Confirmation',
                text: textDeConfirmation,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: icon === 'danger' || icon === 'warning' ? '#d33' : '#198754',
                cancelButtonColor: '#3085d6',
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById(formId);
                    const button = form.querySelector("button");

                    // Désactiver le bouton
                    button.disabled = true;

                    // Remplacer par loader
                    button.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Traitement...
                    `;

                    // Empêcher double clic
                    form.classList.add('submitting');

                    // Soumettre
                    form.submit();
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


<!-- ------------------------------------------------------------------------------------------------------------------------------- -->

<!-- ------------------------------------------------------------------------------------------------------------------------------- -->
 

{{-- 

--}}

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
    </style>

    @if ($errors->any())
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                // 
                showCloseButton: true,
                timer: false, // Désactive la fermeture automatique pour les erreurs de validation
                timerProgressBar: false, // Désactive la barre de progression pour les erreurs de validation
                // 
                // showCloseButton: false,
                // timer: 30000,
                // timerProgressBar: true,
                width: 'auto', // ✅ Largeur automatique selon le contenu
            });

            let errorMessages = [];
            @foreach ($errors->all() as $error)
                errorMessages.push("{!! addslashes($error) !!}");
            @endforeach

            let message = errorMessages.length > 1 ? "• " + errorMessages.join("<br>• ") : errorMessages[0];

            Toast.fire({
                icon: "error",
                title: "Erreur de validation",
                html: message,
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



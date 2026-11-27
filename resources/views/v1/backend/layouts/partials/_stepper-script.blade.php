<script>
    const form = document.getElementById('{{ $formId }}'); // Sélection du formulaire par son ID

    // Sélection des éléments du stepper
    const stepContents = document.querySelectorAll('[data-step-content]'); // Sélection de tous les contenus d'étape
    const stepItems    = document.querySelectorAll('[data-step]'); // Sélection de tous les éléments d'étape

    // Nombre d'étapes calculé automatiquement en fonction du nombre de [data-step-content]
    const totalSteps = stepContents.length;
    let currentStep = 1;
    const btnPrev      = document.getElementById('btn-prev');
    const btnNext      = document.getElementById('btn-next');
    const btnSubmit    = document.getElementById('btn-submit');

    // Fonction pour naviguer vers une étape spécifique
    function goToStep(step) {
        stepContents.forEach(el => el.classList.add('d-none'));
        document.querySelector(`[data-step-content="${step}"]`).classList.remove('d-none');

        stepItems.forEach(el => {
            el.classList.remove('active', 'done');
            const s = parseInt(el.dataset.step);
            if (s < step)  el.classList.add('done');
            if (s === step) el.classList.add('active');
        });

        btnPrev.classList.toggle('d-none', step === 1);
        btnNext.classList.toggle('d-none', step === totalSteps);
        btnSubmit.classList.toggle('d-none', step !== totalSteps);

        currentStep = step;
    }

    // Validation de l'étape actuelle avant de passer à l'étape suivante
    function validateStep(step) {
        const stepContent = document.querySelector(`[data-step-content="${step}"]`);
        const fields = stepContent.querySelectorAll('input, textarea, select');

        // 1. Champs SummerNote (textarea masqué par l'éditeur riche) : validation manuelle
        //    car le navigateur ne peut pas valider un textarea caché (display:none).
        const summernoteInvalid = Array.from(fields).find((field) => {
            if (field.tagName !== 'TEXTAREA' || !field.id.startsWith('summernote-')) {
                return false;
            }
            // Nettoyage d'une éventuelle erreur précédente
            clearSummernoteError(field);
            if (field.required && field.value.trim() === '') {
                showSummernoteError(field);
                return true;
            }
            return false;
        });

        if (summernoteInvalid) {
            summernoteInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        // 2. Champs natifs (input, select, textarea non-SummerNote)
        const invalidField = Array.from(fields).find((field) => {
            if (field.tagName === 'TEXTAREA' && field.id.startsWith('summernote-')) {
                return false; // déjà géré ci-dessus
            }
            return field.willValidate && !field.disabled && !field.checkValidity();
        });

        if (invalidField) {
            invalidField.reportValidity();
            invalidField.focus();
            return false;
        }

        return true;
    }

    // Affiche un message d'erreur visible sous l'éditeur SummerNote
    function showSummernoteError(textarea) {
        const wrapper = textarea.closest('.note-editor, .note-frame, .summernote-wrapper') || textarea.parentElement;
        if (!wrapper) return;
        let feedback = wrapper.querySelector('.summernote-invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'summernote-invalid-feedback text-danger small mt-1';
            wrapper.appendChild(feedback);
        }
        feedback.textContent = 'Veuillez renseigner ce champ.';
        textarea.classList.add('is-invalid');
    }

    // Supprime le message d'erreur d'un champ SummerNote
    function clearSummernoteError(textarea) {
        const wrapper = textarea.closest('.note-editor, .note-frame, .summernote-wrapper') || textarea.parentElement;
        if (!wrapper) return;
        const feedback = wrapper.querySelector('.summernote-invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
        textarea.classList.remove('is-invalid');
    }

    // Trouver le premier champ invalide dans le formulaire
    function findFirstInvalidField() {
        return form.querySelector(':invalid');
    }

    // Gestion des événements pour les boutons "Précédent" et "Suivant"
    btnNext.addEventListener('click', () => {
        if (currentStep < totalSteps && validateStep(currentStep)) {
            goToStep(currentStep + 1);
        }
    });

    // Gestion de l'événement pour le bouton "Précédent"
    btnPrev.addEventListener('click', () => {
        if (currentStep > 1) goToStep(currentStep - 1);
    });

    // Gestion de l'événement pour la soumission du formulaire
    form.addEventListener('submit', (event) => {
        // Validation manuelle des champs SummerNote avant la soumission
        const summernoteFields = form.querySelectorAll('textarea[id^="summernote-"]');
        let summernoteInvalid = null;
        summernoteFields.forEach((field) => {
            clearSummernoteError(field);
            if (field.required && field.value.trim() === '') {
                showSummernoteError(field);
                if (!summernoteInvalid) {
                    summernoteInvalid = field;
                }
            }
        });

        if (summernoteInvalid) {
            event.preventDefault();
            const invalidStep = summernoteInvalid.closest('[data-step-content]');
            if (invalidStep) {
                goToStep(parseInt(invalidStep.dataset.stepContent));
            }
            summernoteInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        if (!form.reportValidity()) {
            event.preventDefault();
            const invalidField = findFirstInvalidField();

            if (invalidField) {
                const invalidStep = invalidField.closest('[data-step-content]');

                if (invalidStep) {
                    const step = parseInt(invalidStep.dataset.stepContent);
                    goToStep(step);
                }

                invalidField.focus();
            }
        }
    });

    goToStep(1); // Initialisation à la première étape

</script>
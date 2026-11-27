<script>
    const form = document.getElementById('{{ $formId }}'); // Sélection du formulaire par son ID

    // SummerNote masque le <textarea required> : le navigateur ne peut ni le valider ni le focus,
    // et bloquerait l'envoi sans message. On retire donc l'attribut `required` de ces textareas
    // (mémorisé dans data-sn-required) et on les valide manuellement plus bas.
    // La validation native reste active pour tous les autres champs.
    form.querySelectorAll('textarea[id^="summernote-"]').forEach((field) => {
        if (field.required) {
            field.dataset.snRequired = '1';
            field.required = false;
        }
    });

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

    // ---------------------------------------------------------------------
    // SummerNote : helpers
    // ---------------------------------------------------------------------

    function isSummernoteTextarea(field) {
        return field.tagName === 'TEXTAREA' && field.id.startsWith('summernote-');
    }

    // Retourne le bloc .note-editor associé à un textarea SummerNote
    function getSummernoteEditor(textarea) {
        const next = textarea.nextElementSibling;
        if (next && next.classList.contains('note-editor')) {
            return next;
        }
        return textarea.parentElement ? textarea.parentElement.querySelector('.note-editor') : null;
    }

    // Retourne le contenu HTML réel de l'éditeur (et non la valeur du textarea, qui peut être
    // désynchronisée ou contenir du HTML "vide" comme <p><br></p>).
    function getSummernoteHtml(textarea) {
        const editor = getSummernoteEditor(textarea);
        if (!editor) return textarea.value;

        if (editor.classList.contains('codeview')) {
            const codable = editor.querySelector('.note-codable');
            return codable ? codable.value : textarea.value;
        }
        const editable = editor.querySelector('.note-editable');
        return editable ? editable.innerHTML : textarea.value;
    }

    // Vrai si l'éditeur ne contient aucun texte ni média : gère <p><br></p>, &nbsp;, espaces...
    function isSummernoteEmpty(textarea) {
        const tmp = document.createElement('div');
        tmp.innerHTML = getSummernoteHtml(textarea);
        const hasMedia = tmp.querySelector('img, video, iframe, embed, object, table, hr');
        const text = (tmp.textContent || '').replace(/\u00a0/g, ' ').trim();
        return text === '' && !hasMedia;
    }

    function getSummernoteErrorWrapper(textarea) {
        return textarea.closest('.summernote-wrapper') || textarea.parentElement;
    }

    // Affiche un message d'erreur visible sous l'éditeur SummerNote
    function showSummernoteError(textarea) {
        const wrapper = getSummernoteErrorWrapper(textarea);
        if (!wrapper) return;
        let feedback = wrapper.querySelector('.summernote-invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'summernote-invalid-feedback text-danger small mt-1';
            wrapper.appendChild(feedback);
        }
        feedback.textContent = 'Veuillez renseigner ce champ.';
        textarea.classList.add('is-invalid');
        const editor = getSummernoteEditor(textarea);
        if (editor) editor.style.border = '1px solid #dc3545';
    }

    // Supprime le message d'erreur d'un champ SummerNote
    function clearSummernoteError(textarea) {
        const wrapper = getSummernoteErrorWrapper(textarea);
        if (wrapper) {
            const feedback = wrapper.querySelector('.summernote-invalid-feedback');
            if (feedback) feedback.remove();
        }
        textarea.classList.remove('is-invalid');
        const editor = getSummernoteEditor(textarea);
        if (editor) editor.style.border = '';
    }

    // Place le curseur dans l'éditeur SummerNote et le rend visible
    function focusSummernote(textarea) {
        const editor = getSummernoteEditor(textarea);
        const target = editor || textarea;
        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        const editable = editor ? editor.querySelector('.note-editable') : null;
        if (editable) editable.focus();
    }

    // Valide les champs SummerNote d'un conteneur. Retourne le premier champ invalide (ou null).
    // Les champs vides non requis sont normalisés à '' (au lieu de <p><br></p>).
    function validateSummernoteFields(container) {
        let firstInvalid = null;
        container.querySelectorAll('textarea[id^="summernote-"]').forEach((field) => {
            clearSummernoteError(field);
            if (isSummernoteEmpty(field)) {
                field.value = '';
                if (field.dataset.snRequired === '1') {
                    showSummernoteError(field);
                    if (!firstInvalid) firstInvalid = field;
                }
            }
        });
        return firstInvalid;
    }

    // Efface l'erreur dès que l'utilisateur saisit du contenu dans l'éditeur
    function onSummernoteInput(event) {
        const editor = event.target.closest ? event.target.closest('.note-editor') : null;
        if (!editor) return;
        const textarea = editor.previousElementSibling;
        if (textarea && isSummernoteTextarea(textarea) && !isSummernoteEmpty(textarea)) {
            clearSummernoteError(textarea);
        }
    }
    form.addEventListener('input', onSummernoteInput);
    form.addEventListener('keyup', onSummernoteInput);

    // ---------------------------------------------------------------------
    // Onglets (ex : onglets de langues FR / EN dans une étape)
    // ---------------------------------------------------------------------

    // Active automatiquement le(s) onglet(s) Bootstrap contenant le champ, s'il est caché.
    // Retourne true si au moins un onglet a été basculé.
    function revealField(field) {
        let switched = false;
        let pane = field.closest('.tab-pane');

        while (pane) {
            if (!pane.classList.contains('active') && pane.id) {
                const id = CSS.escape(pane.id);
                const trigger = document.querySelector(
                    `[data-bs-toggle="tab"][href="#${id}"], [data-bs-toggle="tab"][data-bs-target="#${id}"]`
                );
                if (trigger) {
                    if (window.bootstrap && window.bootstrap.Tab) {
                        window.bootstrap.Tab.getOrCreateInstance(trigger).show();
                    } else {
                        trigger.click();
                    }
                    switched = true;
                }
            }
            pane = pane.parentElement ? pane.parentElement.closest('.tab-pane') : null;
        }

        return switched;
    }

    // Rend le champ invalide visible (bascule d'onglet si besoin) puis le met en évidence
    function focusInvalidField(field) {
        const switched = revealField(field);

        const run = () => {
            if (isSummernoteTextarea(field)) {
                focusSummernote(field);
            } else {
                field.focus();
                field.reportValidity();
            }
        };

        // Petit délai si un onglet vient d'être basculé (le temps que le champ devienne visible)
        if (switched) {
            setTimeout(run, 60);
        } else {
            run();
        }
    }

    // ---------------------------------------------------------------------
    // Validation
    // ---------------------------------------------------------------------

    function isFieldInvalid(field) {
        if (isSummernoteTextarea(field)) {
            return field.dataset.snRequired === '1' && isSummernoteEmpty(field);
        }
        return field.willValidate && !field.disabled && !field.checkValidity();
    }

    // Valide tous les champs d'un conteneur (étape ou formulaire entier).
    // Marque les erreurs SummerNote, puis retourne le premier champ invalide dans l'ordre du DOM.
    function findFirstInvalid(container) {
        validateSummernoteFields(container);
        const fields = container.querySelectorAll('input, textarea, select');
        return Array.from(fields).find(isFieldInvalid) || null;
    }

    // Affiche le champ invalide : va à son étape, bascule l'onglet, met le focus + message
    function showInvalid(field) {
        const step = field.closest('[data-step-content]');
        if (step) {
            goToStep(parseInt(step.dataset.stepContent));
        }
        focusInvalidField(field);
    }

    // Validation de l'étape actuelle avant de passer à l'étape suivante
    function validateStep(step) {
        const stepContent = document.querySelector(`[data-step-content="${step}"]`);
        const invalid = findFirstInvalid(stepContent);
        if (invalid) {
            focusInvalidField(invalid);
            return false;
        }
        return true;
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

    // Au clic sur "Enregistrer" : on valide tout le formulaire AVANT la validation native du
    // navigateur (qui échouerait en silence sur un champ caché dans une autre étape/un autre onglet).
    btnSubmit.addEventListener('click', (event) => {
        const invalid = findFirstInvalid(form);
        if (invalid) {
            event.preventDefault();
            showInvalid(invalid);
        }
    });

    // Filet de sécurité (ex : soumission par la touche Entrée)
    form.addEventListener('submit', (event) => {
        const invalid = findFirstInvalid(form);
        if (invalid) {
            event.preventDefault();
            showInvalid(invalid);
        }
    });

    goToStep(1); // Initialisation à la première étape

</script>
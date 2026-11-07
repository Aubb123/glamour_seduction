<script>
    // Affiche / masque le champ « Code couleur » selon le type de l'attribut parent sélectionné.
    (function () {
        const select  = document.getElementById('attribute_id');
        const wrapper = document.getElementById('hex-color-wrapper');
        const hexInput = document.getElementById('hex_color');
        const picker   = document.getElementById('hex_color_picker');

        if (!select || !wrapper || !hexInput) return;

        // Bascule l'affichage + le caractère obligatoire selon l'option choisie (data-type)
        function syncHexVisibility() {
            const option  = select.options[select.selectedIndex];
            const isColor = !!option && option.dataset.type === 'color';

            wrapper.classList.toggle('d-none', !isColor);
            hexInput.required = isColor;
        }

        select.addEventListener('change', syncHexVisibility);

        // Synchronise le sélecteur de couleur natif avec le champ texte (formatage en MAJUSCULES)
        if (picker) {
            picker.addEventListener('input', function () {
                hexInput.value = picker.value.toUpperCase();
            });
        }

        // État initial (édition avec un attribut couleur déjà sélectionné)
        syncHexVisibility();
    })();
</script>

<script>
    /**
     * Filtre les divisions de dernier niveau (celles qui n'ont aucun enfant)
     * en fonction du pays sélectionné.
     *
     * Le pays est choisi par l'utilisateur ; seules les divisions dont
     * `data-has-children` vaut « 0 » et dont `data-country` correspond au pays
     * sont affichées. Aucun filtre n'est appliqué côté serveur.
     */
    (function () {
        const countrySelect  = document.getElementById('{{ $countryId ?? 'country_id' }}');
        const divisionSelect = document.getElementById('{{ $divisionId ?? 'division_id' }}');
        if (!countrySelect || !divisionSelect) return;

        const options = Array.from(divisionSelect.querySelectorAll('option[data-country]'));

        function isLastLevel(option) {
            return option.dataset.hasChildren !== '1';
        }

        function filterDivisions() {
            const countryId = countrySelect.value;
            let firstVisible = null;

            options.forEach(function (option) {
                const match = countryId !== '' && option.dataset.country === countryId && isLastLevel(option);
                option.hidden = !match;
                option.disabled = !match;
                if (match && firstVisible === null) firstVisible = option.value;
            });

            // Conserve la sélection courante si elle reste visible, sinon bascule
            // sur la première division du pays (ou vide si le pays n'a pas de division).
            const current = options.find(function (option) { return option.value === divisionSelect.value; });
            if (!current || current.hidden) {
                divisionSelect.value = firstVisible !== null ? firstVisible : '';
            }
        }

        countrySelect.addEventListener('change', filterDivisions);
        filterDivisions();
    })();
</script>
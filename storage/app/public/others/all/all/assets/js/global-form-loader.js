// ═══════════════════════════════════════════════════════════════
// PRE-LOADER — masqué dès que le DOM est prêt, sans attendre les images
// (ce script est chargé SANS le type Rocket Loader, donc il s'exécute immédiatement)
// ═══════════════════════════════════════════════════════════════
document.addEventListener("DOMContentLoaded", function () {
    var loader = document.getElementById('global-loader');
    if (loader) {
        loader.style.display = 'none';
    }
});

// document.addEventListener("DOMContentLoaded", function () {

//     // Cible TOUS les formulaires du site
//     document.querySelectorAll("form").forEach(function(form) {

//         form.addEventListener("submit", function() {

//             // Trouver tous les boutons submit du formulaire
//             const buttons = form.querySelectorAll("button[type='submit']");

//             buttons.forEach(function(button) {

//                 // Empêcher double soumission
//                 button.disabled = true;

//                 // Sauvegarder texte original
//                 const originalText = button.innerHTML;

//                 // Remplacer par loader
//                 button.innerHTML = `
//                     <span class="spinner-border spinner-border-sm me-2"></span>
//                     En cours...
//                 `;

//                 // Optionnel : remettre si erreur JS (fallback)
//                 setTimeout(() => {
//                     button.disabled = false;
//                     button.innerHTML = originalText;
//                 }, 15000);

//             });

//         });

//     });

// });



// document.addEventListener("DOMContentLoaded", function () {

//     document.querySelectorAll("form").forEach(function(form) {

//         form.addEventListener("submit", function(e) {

//             // Empêcher double soumission
//             if (form.classList.contains("submitting")) {
//                 e.preventDefault();
//                 return;
//             }

//             form.classList.add("submitting");

//             const buttons = form.querySelectorAll("button[type='submit']");

//             buttons.forEach(function(button) {

//                 button.disabled = true;

//                 button.innerHTML = `
//                     <span class="spinner-border spinner-border-sm me-2"></span>
//                     En cours...
//                 `;

//             });

//         });

//     });

// });


document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll("form").forEach(function(form) {

        form.addEventListener("submit", function() {

            const buttons = form.querySelectorAll("button[type='submit']");

            buttons.forEach(function(button) {

                // ✅ PAS de disabled !
                button.style.pointerEvents = "none";

                const originalText = button.innerHTML;

                button.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    En cours...
                `;

                // Fallback 15s
                setTimeout(() => {
                    button.style.pointerEvents = "";
                    button.innerHTML = originalText;
                }, 15000);

            });

        });

    });

});


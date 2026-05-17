document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-link');
    const contents = document.querySelectorAll('.tab-content');

    // La logique de clic sur les onglets
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-tab');

            // 1. Enlever la classe active de tous les boutons et contenus
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));

            // 2. Ajouter la classe active au bouton cliqué et au contenu cible
            tab.classList.add('active');
            document.getElementById(target).classList.add('active');
        });
    });

    // NOUVEAU : Réouverture automatique de l'onglet si l'URL le demande
    if (window.location.hash) {
        // Cherche le bouton qui correspond à l'ancre de l'URL (ex: #notes-tab)
        const targetTab = document.querySelector(window.location.hash);
        if (targetTab) {
            targetTab.click(); // Simule un clic pour ouvrir l'onglet instantanément
        }
    }
    // Gestion de la confirmation de suppression des notes
    const deleteForms = document.querySelectorAll('.delete-note-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Delete this note?')) {
                e.preventDefault(); // Annule l'envoi si l'utilisateur clique sur "Annuler"
            }
        });
    });
});


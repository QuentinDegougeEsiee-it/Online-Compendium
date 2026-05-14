document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-link');
    const contents = document.querySelectorAll('.tab-content');

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
});
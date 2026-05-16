const searchInput = document.getElementById('searchInput');
const monsterItems = document.querySelectorAll('.monster-item');
const noResultsMessage = document.getElementById('noResults');

// Cibler les menus déroulants
const elementBtn = document.getElementById('elementBtn');
const elementDropdown = document.getElementById('elementDropdown');
const elementCheckboxes = document.querySelectorAll('#elementDropdown input[type="checkbox"]');

const speciesBtn = document.getElementById('speciesBtn');
const speciesDropdown = document.getElementById('speciesDropdown');
const speciesCheckboxes = document.querySelectorAll('#speciesDropdown input[type="checkbox"]');

const typeBtn = document.getElementById('typeBtn');
const typeDropdown = document.getElementById('typeDropdown');
const typeCheckboxes = document.querySelectorAll('#typeDropdown input[type="checkbox"]');

// Habitats
const locationBtn = document.getElementById('locationBtn');
const locationDropdown = document.getElementById('locationDropdown');
const locationCheckboxes = document.querySelectorAll('#locationDropdown input[type="checkbox"]');

// --- NOUVEAU : Gestion de l'ouverture/fermeture des menus (Un seul à la fois) ---
const allDropdowns = [elementDropdown, speciesDropdown, typeDropdown, locationDropdown];

function toggleDropdown(event, targetDropdown) {
    event.stopPropagation(); // Empêche le clic de déclencher la fermeture globale
    
    // 1. On ferme tous les autres menus
    allDropdowns.forEach(dropdown => {
        if (dropdown !== targetDropdown) {
            dropdown.classList.remove('show');
        }
    });
    
    // 2. On ouvre ou ferme le menu cliqué
    targetDropdown.classList.toggle('show');
}

// On attache la nouvelle fonction aux boutons
elementBtn.addEventListener('click', (e) => toggleDropdown(e, elementDropdown));
speciesBtn.addEventListener('click', (e) => toggleDropdown(e, speciesDropdown));
typeBtn.addEventListener('click', (e) => toggleDropdown(e, typeDropdown));
locationBtn.addEventListener('click', (e) => toggleDropdown(e, locationDropdown));

// BONUS UX : Fermer les menus si on clique n'importe où ailleurs sur la page
document.addEventListener('click', () => {
    allDropdowns.forEach(dropdown => dropdown.classList.remove('show'));
});

// Sécurité : Ne pas fermer le menu si on clique à l'intérieur (pour cocher une case)
allDropdowns.forEach(dropdown => {
    dropdown.addEventListener('click', (e) => e.stopPropagation());
});

// Fonction de filtrage global
function filterMonsters() {
    const searchTerm = searchInput.value.toLowerCase();
    
    const checkedElements = Array.from(elementCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
    const checkedSpecies = Array.from(speciesCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
    const checkedTypes = Array.from(typeCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
    const checkedLocations = Array.from(locationCheckboxes).filter(cb => cb.checked).map(cb => cb.value);

    let hasVisibleMonsters = false;

    monsterItems.forEach(item => {
        const monsterName = item.querySelector('.monster-name').textContent.toLowerCase();
        const monsterElements = item.getAttribute('data-elements').toLowerCase();
        const monsterSpecies = item.getAttribute('data-species').toLowerCase();
        const monsterType = item.getAttribute('data-type').toLowerCase();
        const monsterLocations = item.getAttribute('data-locations').toLowerCase();

        // 1. Nom
        const matchesName = monsterName.includes(searchTerm);

        // 2. Éléments (Logique ET)
        let matchesElements = true;
        if (checkedElements.length > 0) {
            matchesElements = checkedElements.every(el => monsterElements.includes(el));
        }

        // 3. Espèces (Logique ET)
        let matchesSpecies = true;
        if (checkedSpecies.length > 0) {
            matchesSpecies = checkedSpecies.every(sp => monsterSpecies.includes(sp));
        }

        // 4. Taille/Type (Logique ET)
        let matchesType = true;
        if (checkedTypes.length > 0) {
            matchesType = checkedTypes.every(t => monsterType.includes(t));
        }

        // 5. Habitats (Logique OU avec 'some')
        let matchesLocations = true;
        if (checkedLocations.length > 0) {
            matchesLocations = checkedLocations.some(loc => monsterLocations.includes(loc));
        }

        // Validation finale
        if (matchesName && matchesElements && matchesSpecies && matchesType && matchesLocations) {
            item.style.display = '';
            hasVisibleMonsters = true;
        } else {
            item.style.display = 'none';
        }
    });

    noResultsMessage.style.display = hasVisibleMonsters ? 'none' : 'block';
}

// Écouteurs d'événements
searchInput.addEventListener('input', filterMonsters);
elementCheckboxes.forEach(cb => cb.addEventListener('change', filterMonsters));
speciesCheckboxes.forEach(cb => cb.addEventListener('change', filterMonsters));
typeCheckboxes.forEach(cb => cb.addEventListener('change', filterMonsters));
locationCheckboxes.forEach(cb => cb.addEventListener('change', filterMonsters));
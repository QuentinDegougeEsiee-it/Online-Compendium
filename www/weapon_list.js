document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('weaponSearchInput');
    const rankCheckboxes = document.querySelectorAll('.rank-checkbox');
    const elementCheckboxes = document.querySelectorAll('.element-checkbox');
    const weaponItems = document.querySelectorAll('.weapon-item');

    // --- GESTION DES MENUS DÉROULANTS ---
    const rankBtn = document.getElementById('rankFilterBtn');
    const rankContent = document.getElementById('rankFilterContent');
    const elementBtn = document.getElementById('elementFilterBtn');
    const elementContent = document.getElementById('elementFilterContent');

    function setupDropdown(btn, content) {
        if(btn && content) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                // Ferme l'autre menu s'il est ouvert
                if (content === rankContent) elementContent.classList.remove('show');
                if (content === elementContent) rankContent.classList.remove('show');
                content.classList.toggle('show');
            });
        }
    }

    setupDropdown(rankBtn, rankContent);
    setupDropdown(elementBtn, elementContent);

    // Ferme les menus si on clique ailleurs sur la page
    document.addEventListener('click', function(e) {
        if(rankContent && !rankContent.contains(e.target) && e.target !== rankBtn) {
            rankContent.classList.remove('show');
        }
        if(elementContent && !elementContent.contains(e.target) && e.target !== elementBtn) {
            elementContent.classList.remove('show');
        }
    });

    // --- LOGIQUE DE FILTRAGE MULTICRITÈRES ---
    function filterWeapons() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        
        // Récupère les rangs cochés
        const checkedRanks = Array.from(rankCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
            
        // Récupère les éléments cochés
        const checkedElements = Array.from(elementCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        weaponItems.forEach(item => {
            const itemName = item.getAttribute('data-name');
            const itemRarity = item.getAttribute('data-rarity');
            const itemElementsAttr = item.getAttribute('data-elements');
            
            // Transforme la liste "fire,poison" en vrai tableau Javascript ['fire', 'poison']
            const itemElements = itemElementsAttr ? itemElementsAttr.split(',') : [];

            // 1. Vérification de la recherche textuelle
            const matchesSearch = itemName.includes(searchTerm);

            // 2. Vérification du Rang (Si aucun coché, on affiche tout)
            const matchesRank = checkedRanks.length === 0 || checkedRanks.includes(itemRarity);

            // 3. Vérification de l'Élément (Doit posséder TOUS les éléments cochés)
            let matchesElements = true;
            if (checkedElements.length > 0) {
                for (const el of checkedElements) {
                    if (!itemElements.includes(el)) {
                        matchesElements = false; // Il manque un des éléments cochés, on cache !
                        break;
                    }
                }
            }

            // Affichage final : l'arme doit passer les 3 tests !
            if (matchesSearch && matchesRank && matchesElements) {
                item.style.display = 'flex'; // On restaure l'affichage flex d'origine
            } else {
                item.style.display = 'none'; // On cache la carte
            }
        });
    }

    // --- DÉCLENCHEURS ---
    if(searchInput) searchInput.addEventListener('input', filterWeapons);
    rankCheckboxes.forEach(cb => cb.addEventListener('change', filterWeapons));
    elementCheckboxes.forEach(cb => cb.addEventListener('change', filterWeapons));
});
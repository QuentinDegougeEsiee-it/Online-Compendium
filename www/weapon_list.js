document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('weaponSearchInput');
    const rankCheckboxes = document.querySelectorAll('.rank-checkbox');
    const elementCheckboxes = document.querySelectorAll('.element-checkbox');
    const weaponItems = document.querySelectorAll('.weapon-item');

    // --- GESTION DES MENUS DÉROULANTS (FILTRES) ---
    const rankBtn = document.getElementById('rankFilterBtn');
    const rankContent = document.getElementById('rankFilterContent');
    const elementBtn = document.getElementById('elementFilterBtn');
    const elementContent = document.getElementById('elementFilterContent');

    // NOUVEAU : Interrupteurs du Tri
    const sortBtn = document.getElementById('sortBtn');
    const sortContent = document.getElementById('sortContent');
    const sortOptions = document.querySelectorAll('.sort-option');
    const container = document.querySelector('.weapon-list-container');

    function setupDropdown(btn, content) {
        if(btn && content) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                // Ferme les autres menus quand on en ouvre un
                if (content !== rankContent) rankContent.classList.remove('show');
                if (content !== elementContent) elementContent.classList.remove('show');
                if (content !== sortContent && sortContent) sortContent.classList.remove('show');
                content.classList.toggle('show');
            });
        }
    }

    setupDropdown(rankBtn, rankContent);
    setupDropdown(elementBtn, elementContent);
    setupDropdown(sortBtn, sortContent); // Liaison du bouton de tri

    // Ferme les menus si on clique n'importe où ailleurs sur la page
    document.addEventListener('click', function(e) {
        if(rankContent && !rankContent.contains(e.target) && e.target !== rankBtn) {
            rankContent.classList.remove('show');
        }
        if(elementContent && !elementContent.contains(e.target) && e.target !== elementBtn) {
            elementContent.classList.remove('show');
        }
        if(sortContent && !sortContent.contains(e.target) && e.target !== sortBtn) {
            sortContent.classList.remove('show');
        }
    });

    // --- LOGIQUE DE FILTRAGE MULTICRITÈRES ---
    function filterWeapons() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        
        const checkedRanks = Array.from(rankCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
            
        const checkedElements = Array.from(elementCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        weaponItems.forEach(item => {
            const itemName = item.getAttribute('data-name');
            const itemRarity = item.getAttribute('data-rarity');
            const itemElementsAttr = item.getAttribute('data-elements');
            
            const itemElements = itemElementsAttr ? itemElementsAttr.split(',') : [];

            const matchesSearch = itemName.includes(searchTerm);
            const matchesRank = checkedRanks.length === 0 || checkedRanks.includes(itemRarity);

            // Doit posséder STRICTEMENT TOUS les éléments cochés
            let matchesElements = true;
            if (checkedElements.length > 0) {
                for (const el of checkedElements) {
                    if (!itemElements.includes(el)) {
                        matchesElements = false;
                        break;
                    }
                }
            }

            if (matchesSearch && matchesRank && matchesElements) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // --- NOUVEAU : ALGORITHME DE TRI DYNAMIQUE ---
    sortOptions.forEach(option => {
        option.addEventListener('click', function() {
            const criteria = this.getAttribute('data-sort');
            // On récupère la liste fraîche des éléments du DOM
            const itemsArray = Array.from(container.querySelectorAll('.weapon-item'));

            itemsArray.sort((a, b) => {
                const attackA = parseInt(a.getAttribute('data-attack'));
                const attackB = parseInt(b.getAttribute('data-attack'));
                const rankA = parseInt(a.getAttribute('data-rarity'));
                const rankB = parseInt(b.getAttribute('data-rarity'));
                const sharpA = parseInt(a.getAttribute('data-sharpness'));
                const sharpB = parseInt(b.getAttribute('data-sharpness'));
                const elementA = a.getAttribute('data-elements') || 'zzz';
                const elementB = b.getAttribute('data-elements') || 'zzz';

                switch(criteria) {
                    case 'attack-desc': return attackB - attackA;
                    case 'attack-asc':  return attackA - attackB;
                    case 'rank-desc':   return rankB - rankA;
                    case 'rank-asc':    return rankA - rankB;
                    case 'sharpness-desc': return sharpB - sharpA;
                    case 'sharpness-asc':  return sharpA - sharpB;
                    case 'element-asc': return elementA.localeCompare(elementB);
                    default: return 0;
                }
            });

            // Ré-injection physique ordonnée dans la boîte parent du DOM
            itemsArray.forEach(item => container.appendChild(item));
            
            // Personnalise l'en-tête du menu pour afficher le tri courant
            if (sortBtn) sortBtn.innerHTML = this.textContent + ' ▾';
            
            sortContent.classList.remove('show');
        });
    });

    // --- DÉCLENCHEURS ---
    if(searchInput) searchInput.addEventListener('input', filterWeapons);
    rankCheckboxes.forEach(cb => cb.addEventListener('change', filterWeapons));
    elementCheckboxes.forEach(cb => cb.addEventListener('change', filterWeapons));
});
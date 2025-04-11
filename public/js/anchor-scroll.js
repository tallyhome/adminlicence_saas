/**
 * Script pour gérer le défilement fluide vers les ancres dans la documentation
 */
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour trouver un élément par son ID ou par son texte normalisé
    function findTargetElement(targetId) {
        // D'abord, essayez de trouver l'élément par son ID exact
        let targetElement = document.getElementById(targetId);
        
        // Si l'élément n'est pas trouvé, essayez de trouver un titre avec un ID similaire
        if (!targetElement) {
            // Convertir les espaces et caractères spéciaux en tirets
            const normalizedId = targetId.toLowerCase()
                .replace(/[\s]+/g, '-')        // Espaces en tirets
                .replace(/[^\w\-]+/g, '')      // Supprime les caractères spéciaux
                .replace(/\-\-+/g, '-')         // Remplace les tirets multiples par un seul
                .replace(/^-+/, '')            // Supprime les tirets au début
                .replace(/-+$/, '');           // Supprime les tirets à la fin
            
            // Chercher tous les titres (h1-h6)
            const headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
            
            // Parcourir tous les titres pour trouver une correspondance
            for (const heading of headings) {
                const headingId = heading.id;
                const headingText = heading.textContent.toLowerCase()
                    .replace(/[\s]+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '');
                
                // Vérifier si l'ID du titre ou son texte normalisé correspond à l'ID cible
                if (headingId === normalizedId || headingText === normalizedId) {
                    targetElement = heading;
                    break;
                }
            }
        }
        
        return targetElement;
    }
    
    // Sélectionne tous les liens d'ancrage dans la page
    const links = document.querySelectorAll('a[href^="#"]');
    
    // Ajoute un gestionnaire d'événements à chaque lien d'ancrage
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Récupère l'ID de la cible (sans le #)
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = findTargetElement(targetId);
            
            if (targetElement) {
                // Défilement fluide vers l'élément cible
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Met à jour l'URL avec le fragment
                history.pushState(null, null, `#${targetId}`);
            }
        });
    });
    
    // Gère également le cas où l'URL contient déjà un fragment lors du chargement de la page
    if (window.location.hash) {
        const targetId = window.location.hash.substring(1);
        const targetElement = findTargetElement(targetId);
        
        if (targetElement) {
            // Petit délai pour s'assurer que la page est complètement chargée
            setTimeout(() => {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
        }
    }
});
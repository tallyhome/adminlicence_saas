/**
 * Solution finale pour le problème des notifications
 * Cette solution est complètement autonome et ne dépend d'aucun autre script
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Solution finale pour les notifications chargée');
    
    // Fonction pour créer un formulaire de soumission directe
    function createDirectForm(notificationId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/fix-notification';
        form.style.display = 'inline';
        
        // Ajouter le champ ID
        const idField = document.createElement('input');
        idField.type = 'hidden';
        idField.name = 'id';
        idField.value = notificationId;
        
        // Ajouter le token CSRF
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Ajouter un champ caché pour stocker l'URL actuelle
        const currentUrlField = document.createElement('input');
        currentUrlField.type = 'hidden';
        currentUrlField.name = 'current_url';
        currentUrlField.value = window.location.href;
        
        form.appendChild(idField);
        form.appendChild(csrfField);
        form.appendChild(currentUrlField);
        
        return form;
    }
    
    // Remplacer tous les boutons "Marquer comme lu"
    document.querySelectorAll('.mark-as-read, .mark-as-read-btn').forEach(function(button) {
        if (!button || button.dataset === undefined) return;
        
        const notificationId = button.dataset.id;
        if (!notificationId) return;
        
        console.log('Remplacement du bouton pour la notification:', notificationId);
        
        // Créer le formulaire
        const form = createDirectForm(notificationId);
        
        // Créer un nouveau bouton qui ressemble à l'original
        const newButton = document.createElement('button');
        newButton.type = 'submit';
        newButton.className = button.className;
        newButton.innerHTML = button.innerHTML || '<i class="fas fa-check me-1"></i>Marquer comme lu';
        
        // Ajouter le bouton au formulaire
        form.appendChild(newButton);
        
        // Remplacer l'ancien bouton par le formulaire
        if (button.parentNode) {
            button.parentNode.replaceChild(form, button);
        }
    });
    
    // Remplacer le bouton "Marquer tout comme lu"
    const markAllButtons = document.querySelectorAll('#markAllAsReadBtn, #mark-all-as-read-btn');
    markAllButtons.forEach(function(button) {
        if (!button) return;
        
        console.log('Remplacement du bouton "Marquer tout comme lu"');
        
        // Créer le formulaire
        const form = createDirectForm('all');
        
        // Créer un nouveau bouton qui ressemble à l'original
        const newButton = document.createElement('button');
        newButton.type = 'submit';
        newButton.id = button.id;
        newButton.className = button.className;
        newButton.innerHTML = button.innerHTML || '<i class="fas fa-check-double me-1"></i>Marquer tout comme lu';
        
        // Ajouter le bouton au formulaire
        form.appendChild(newButton);
        
        // Remplacer l'ancien bouton par le formulaire
        if (button.parentNode) {
            button.parentNode.replaceChild(form, button);
        }
    });
    
    // Désactiver tous les autres scripts de notification qui pourraient interférer
    window.stopNotificationScripts = function() {
        // Arrêter les intervalles qui pourraient causer des problèmes
        for (let i = 1; i < 10000; i++) {
            window.clearInterval(i);
        }
        
        // Supprimer les écouteurs d'événements problématiques
        const problematicButtons = document.querySelectorAll('.mark-as-read, .mark-as-read-btn');
        problematicButtons.forEach(function(button) {
            if (button) {
                const newButton = button.cloneNode(true);
                if (button.parentNode) {
                    button.parentNode.replaceChild(newButton, button);
                }
            }
        });
        
        console.log('Scripts de notification potentiellement problématiques désactivés');
    };
    
    // Exécuter après un court délai pour s'assurer que tous les autres scripts sont chargés
    setTimeout(window.stopNotificationScripts, 500);
});

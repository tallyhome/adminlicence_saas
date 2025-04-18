/**
 * Solution ultra-radicale pour le problème des notifications
 * Version améliorée avec redirection vers la page actuelle
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Solution ultra-radicale pour les notifications chargée - Version 2.0');
    
    // Remplacer tous les boutons "Marquer comme lu" par des formulaires simples
    document.querySelectorAll('.mark-as-read').forEach(button => {
        const notificationId = button.dataset.id;
        if (!notificationId) return;
        
        console.log('Remplacement du bouton pour la notification:', notificationId);
        
        // Créer un formulaire simple
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/fix-notification';
        form.style.display = 'inline';
        
        // Ajouter un champ caché pour stocker l'URL de retour
        const returnUrlField = document.createElement('input');
        returnUrlField.type = 'hidden';
        returnUrlField.name = 'return_url';
        returnUrlField.value = window.location.href;
        
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
        
        // Créer un bouton de soumission qui ressemble au bouton original
        const submitButton = document.createElement('button');
        submitButton.type = 'submit';
        submitButton.className = button.className;
        submitButton.innerHTML = button.innerHTML;
        
        // Ajouter les éléments au formulaire
        form.appendChild(idField);
        form.appendChild(csrfField);
        form.appendChild(returnUrlField);
        form.appendChild(submitButton);
        
        // Remplacer le bouton original par le formulaire
        button.parentNode.replaceChild(form, button);
    });
    
    // Remplacer le bouton "Marquer tout comme lu"
    const markAllBtn = document.getElementById('markAllAsReadBtn');
    if (markAllBtn) {
        console.log('Remplacement du bouton "Marquer tout comme lu"');
        
        // Créer un formulaire simple
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/fix-notification';
        form.style.display = 'inline';
        
        // Ajouter un champ caché pour stocker l'URL de retour
        const returnUrlField = document.createElement('input');
        returnUrlField.type = 'hidden';
        returnUrlField.name = 'return_url';
        returnUrlField.value = window.location.href;
        
        // Ajouter le champ ID avec la valeur "all"
        const idField = document.createElement('input');
        idField.type = 'hidden';
        idField.name = 'id';
        idField.value = 'all';
        
        // Ajouter le token CSRF
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Créer un bouton de soumission qui ressemble au bouton original
        const submitButton = document.createElement('button');
        submitButton.type = 'submit';
        submitButton.id = markAllBtn.id;
        submitButton.className = markAllBtn.className;
        submitButton.innerHTML = markAllBtn.innerHTML;
        
        // Ajouter les éléments au formulaire
        form.appendChild(idField);
        form.appendChild(csrfField);
        form.appendChild(returnUrlField);
        form.appendChild(submitButton);
        
        // Remplacer le bouton original par le formulaire
        markAllBtn.parentNode.replaceChild(form, markAllBtn);
    }
});

/**
 * Solution directe pour le problème des notifications
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Solution de notification directe chargée - Version améliorée');
    
    // Fonction pour mettre à jour directement la base de données
    function updateNotificationDirectly(id) {
        console.log('Mise à jour de la notification:', id);
        
        // Afficher un indicateur de chargement
        const loadingOverlay = document.createElement('div');
        loadingOverlay.style.position = 'fixed';
        loadingOverlay.style.top = '0';
        loadingOverlay.style.left = '0';
        loadingOverlay.style.width = '100%';
        loadingOverlay.style.height = '100%';
        loadingOverlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
        loadingOverlay.style.zIndex = '9999';
        loadingOverlay.style.display = 'flex';
        loadingOverlay.style.justifyContent = 'center';
        loadingOverlay.style.alignItems = 'center';
        
        const spinner = document.createElement('div');
        spinner.className = 'spinner-border text-light';
        spinner.setAttribute('role', 'status');
        
        const spinnerText = document.createElement('span');
        spinnerText.className = 'visually-hidden';
        spinnerText.textContent = 'Chargement...';
        
        spinner.appendChild(spinnerText);
        loadingOverlay.appendChild(spinner);
        document.body.appendChild(loadingOverlay);
        
        // Créer un formulaire caché
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/direct-update-notification';
        form.style.display = 'none';
        
        // Ajouter le champ ID
        const idField = document.createElement('input');
        idField.type = 'hidden';
        idField.name = 'id';
        idField.value = id;
        
        // Ajouter le token CSRF
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Ajouter les champs au formulaire
        form.appendChild(idField);
        form.appendChild(csrfField);
        
        // Ajouter le formulaire au document et le soumettre
        document.body.appendChild(form);
        form.submit();
    }
    
    // Remplacer tous les boutons "Marquer comme lu"
    document.querySelectorAll('.mark-as-read').forEach(button => {
        const notificationId = button.dataset.id;
        if (!notificationId) return;
        
        console.log('Remplacement du bouton pour la notification:', notificationId);
        
        // Créer un nouveau bouton qui utilisera notre fonction directe
        const newButton = document.createElement('button');
        newButton.className = button.className;
        newButton.innerHTML = button.innerHTML;
        newButton.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            updateNotificationDirectly(notificationId);
        };
        
        // Remplacer l'ancien bouton par le nouveau
        button.parentNode.replaceChild(newButton, button);
    });
    
    // Remplacer le bouton "Marquer tout comme lu"
    const markAllBtn = document.getElementById('markAllAsReadBtn');
    if (markAllBtn) {
        const newMarkAllBtn = document.createElement('button');
        newMarkAllBtn.id = markAllBtn.id;
        newMarkAllBtn.className = markAllBtn.className;
        newMarkAllBtn.innerHTML = markAllBtn.innerHTML;
        newMarkAllBtn.onclick = function(e) {
            e.preventDefault();
            updateNotificationDirectly('all');
        };
        
        markAllBtn.parentNode.replaceChild(newMarkAllBtn, markAllBtn);
    }
});

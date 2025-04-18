/**
 * Solution radicale pour résoudre le problème des boutons "Marquer comme lu"
 */
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour marquer une notification comme lue
    function markAsRead(id) {
        console.log('Marquage de la notification comme lue:', id);
        
        // Utiliser la route API spéciale de correction
        fetch('/api/fix/notifications/read/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Réponse:', data);
            if (data.success) {
                // Changer l'apparence de la notification
                const notificationItem = document.getElementById('notification-' + id);
                if (notificationItem) {
                    notificationItem.classList.remove('list-group-item-primary');
                    notificationItem.classList.remove('list-group-item-warning');
                    notificationItem.classList.remove('list-group-item-danger');
                    
                    // Cacher le bouton
                    const button = notificationItem.querySelector('.mark-as-read');
                    if (button) {
                        button.style.display = 'none';
                    }
                }
                
                // Afficher un message de succès
                showToast('Notification marquée comme lue');
            } else {
                showToast('Erreur lors du marquage de la notification', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Une erreur est survenue', 'danger');
        });
    }
    
    // Fonction pour marquer toutes les notifications comme lues
    function markAllAsRead() {
        console.log('Marquage de toutes les notifications comme lues');
        
        // Utiliser la route API spéciale de correction
        fetch('/api/fix/notifications/read-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Réponse:', data);
            if (data.success) {
                // Changer l'apparence de toutes les notifications
                document.querySelectorAll('.list-group-item').forEach(item => {
                    item.classList.remove('list-group-item-primary');
                    item.classList.remove('list-group-item-warning');
                    item.classList.remove('list-group-item-danger');
                    
                    // Cacher tous les boutons
                    const button = item.querySelector('.mark-as-read');
                    if (button) {
                        button.style.display = 'none';
                    }
                });
                
                // Désactiver le bouton "Marquer tout comme lu"
                const markAllBtn = document.getElementById('markAllAsReadBtn');
                if (markAllBtn) {
                    markAllBtn.setAttribute('disabled', 'disabled');
                }
                
                // Afficher un message de succès
                showToast('Toutes les notifications ont été marquées comme lues');
            } else {
                showToast('Erreur lors du marquage des notifications', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Une erreur est survenue', 'danger');
        });
    }
    
    // Fonction pour afficher un toast
    function showToast(message, type = 'success') {
        const toastContainer = document.createElement('div');
        toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
        toastContainer.style.zIndex = '5';
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        const toastBody = document.createElement('div');
        toastBody.className = 'd-flex';
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'toast-body';
        messageDiv.textContent = message;
        
        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'btn-close btn-close-white me-2 m-auto';
        closeButton.setAttribute('data-bs-dismiss', 'toast');
        closeButton.setAttribute('aria-label', 'Fermer');
        
        toastBody.appendChild(messageDiv);
        toastBody.appendChild(closeButton);
        toast.appendChild(toastBody);
        toastContainer.appendChild(toast);
        document.body.appendChild(toastContainer);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toastContainer);
        });
    }
    
    // Remplacer tous les boutons "Marquer comme lu" par nos propres boutons
    document.querySelectorAll('.mark-as-read').forEach(button => {
        const notificationId = button.dataset.id;
        
        // Créer un nouveau bouton
        const newButton = document.createElement('button');
        newButton.className = button.className;
        newButton.innerHTML = button.innerHTML;
        newButton.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            markAsRead(notificationId);
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
            markAllAsRead();
        };
        
        markAllBtn.parentNode.replaceChild(newMarkAllBtn, markAllBtn);
    }
    
    console.log('Solution de notification appliquée avec succès');
});

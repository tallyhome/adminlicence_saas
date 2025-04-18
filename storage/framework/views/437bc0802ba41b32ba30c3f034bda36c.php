<!-- Composant de notifications en temps réel -->
<div id="notification-container" class="dropdown">
    <button class="btn btn-link nav-link dropdown-toggle position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        <span id="notification-counter" class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle d-none">0</span>
    </button>
    <ul id="notification-list" class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="width: 400px; max-height: 600px; overflow-y: auto;">
        <li class="dropdown-header d-flex justify-content-between align-items-center">
            <span>Notifications</span>
            <button id="mark-all-as-read-btn" class="btn btn-sm btn-link text-decoration-none" disabled>Tout marquer comme lu</button>
        </li>
        <li><hr class="dropdown-divider"></li>
        <div id="notification-items">
            <li class="py-2 px-4 text-center text-muted">Chargement des notifications...</li>
        </div>
        <li><hr class="dropdown-divider"></li>
        <li class="text-center pb-2">
            <a href="<?php echo e(route('admin.notifications.index')); ?>" class="btn btn-sm btn-outline-primary">Voir toutes les notifications</a>
        </li>
    </ul>
</div>

<!-- Conteneur pour les toasts de notification -->
<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1050;"></div>

<!-- Pas besoin de JavaScript pour la solution finale -->

<!-- Script pour initialiser les variables nécessaires aux notifications -->
<?php use Illuminate\Support\Facades\Auth; ?>
<script>
    // ID de l'utilisateur connecté pour les notifications privées
    window.userId = <?php echo e(Auth::guard('admin')->id() ?? 'null'); ?>;
    
    // Compteur initial de notifications non lues
    let unreadNotificationsCount = 0;
    
    // Fonction pour charger les notifications non lues
    function loadUnreadNotifications() {
        // Utiliser une URL absolue pour éviter les problèmes de chemin relatif
        const url = window.location.protocol + '//' + window.location.host + '/admin/notifications/unread';
        console.log('Chargement des notifications depuis:', url);
        
        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin' // Inclure les cookies pour l'authentification
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Données de notifications reçues:', data);
                const notificationItems = document.getElementById('notification-items');
                const notificationCounter = document.getElementById('notification-counter');
                const markAllAsReadBtn = document.getElementById('mark-all-as-read-btn');
                
                // Vérifier si les données sont valides
                if (!data || typeof data !== 'object') {
                    throw new Error('Format de données invalide');
                }
                
                // Utiliser des valeurs par défaut si les propriétés sont manquantes
                const notifications = data.notifications || [];
                const count = data.count || 0;
                
                // Mettre à jour le compteur
                unreadNotificationsCount = count;
                
                if (unreadNotificationsCount > 0) {
                    notificationCounter.textContent = unreadNotificationsCount > 99 ? '99+' : unreadNotificationsCount;
                    notificationCounter.classList.remove('d-none');
                    markAllAsReadBtn.removeAttribute('disabled');
                    
                    // Afficher les notifications
                    notificationItems.innerHTML = '';
                    
                    notifications.forEach(notification => {
                        const notificationItem = document.createElement('li');
                        notificationItem.className = 'dropdown-item notification-item p-2';
                        notificationItem.dataset.id = notification.id;
                        
                        // Déterminer l'icône en fonction du type de notification
                        let iconClass = 'fas fa-bell text-secondary';
                        if (notification.data && notification.data.action) {
                            iconClass = 'fas fa-key text-primary';
                        } else if (notification.data && notification.data.ticket_id) {
                            iconClass = 'fas fa-ticket-alt text-warning';
                        } else if (notification.data && notification.data.invoice_id) {
                            iconClass = 'fas fa-money-bill text-success';
                        }
                        
                        notificationItem.innerHTML = `
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="${iconClass}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="mb-1">${notification.title}</strong>
                                        <small class="text-muted">${notification.created_at}</small>
                                    </div>
                                    <p class="mb-1 small">${notification.message}</p>
                                </div>
                            </div>
                            <div class="mt-1 d-flex justify-content-between">
                                <button class="btn btn-sm btn-outline-primary mark-as-read-btn" data-id="${notification.id}">
                                    <i class="fas fa-check me-1"></i>Marquer comme lu
                                </button>
                                <a href="${notification.url}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye me-1"></i>Voir
                                </a>
                            </div>
                        `;
                        
                        notificationItems.appendChild(notificationItem);
                    });
                    
                    // Ajouter les écouteurs d'événements pour marquer comme lu
                    document.querySelectorAll('.mark-as-read-btn').forEach(button => {
                        button.addEventListener('click', markAsRead);
                    });
                } else {
                    notificationCounter.classList.add('d-none');
                    markAllAsReadBtn.setAttribute('disabled', 'disabled');
                    notificationItems.innerHTML = '<li class="py-2 px-4 text-center text-muted">Aucune notification non lue</li>';
                    return;
                }
                
                // Ajouter chaque notification à la liste
                data.notifications.forEach(notification => {
                    const notificationItem = document.createElement('li');
                    notificationItem.className = 'dropdown-item notification-item p-2';
                    notificationItem.dataset.id = notification.id;
                    
                    // Déterminer l'icône en fonction du type de notification
                    let iconClass = 'fas fa-bell text-secondary';
                    if (notification.data && notification.data.action) {
                        iconClass = 'fas fa-key text-primary';
                    } else if (notification.data && notification.data.ticket_id) {
                        iconClass = 'fas fa-ticket-alt text-warning';
                    } else if (notification.data && notification.data.invoice_id) {
                        iconClass = 'fas fa-money-bill text-success';
                    }
                    
                    notificationItem.innerHTML = `
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="${iconClass}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="mb-1">${notification.title}</strong>
                                    <small class="text-muted">${notification.created_at}</small>
                                </div>
                                <p class="mb-1 small">${notification.message}</p>
                            </div>
                        </div>
                        <div class="mt-1 d-flex justify-content-between">
                            <button class="btn btn-sm btn-outline-primary mark-as-read-btn" data-id="${notification.id}">
                                <i class="fas fa-check me-1"></i>Marquer comme lu
                            </button>
                            <a href="${notification.url}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye me-1"></i>Voir
                            </a>
                        </div>
                    `;
                    
                    notificationItems.appendChild(notificationItem);
                });
                
                // Ajouter les écouteurs d'événements pour marquer comme lu
                document.querySelectorAll('.mark-as-read-btn').forEach(button => {
                    button.addEventListener('click', markAsRead);
                });
            })
            .catch(error => {
                console.error('Erreur lors du chargement des notifications:', error);
                document.getElementById('notification-items').innerHTML = 
                    '<li class="py-2 px-4 text-center text-danger">Erreur lors du chargement des notifications</li>';
                
                // Afficher les détails de l'erreur dans la console pour le débogage
                console.log('Détails de l\'erreur:', {
                    message: error.message,
                    stack: error.stack,
                    url: window.location.href
                });
            });
    }
    
    // Fonction pour marquer une notification comme lue
    function markAsRead(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const notificationId = this.dataset.id;
        const url = window.location.protocol + '//' + window.location.host + '/api/notifications/read/' + notificationId;
        console.log('Marquage de la notification comme lue via API:', url);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Supprimer la notification de la liste
                const notificationItem = button.closest('.notification-item');
                notificationItem.remove();
                
                // Mettre à jour le compteur
                unreadNotificationsCount--;
                const notificationCounter = document.getElementById('notification-counter');
                
                if (unreadNotificationsCount > 0) {
                    notificationCounter.textContent = unreadNotificationsCount > 99 ? '99+' : unreadNotificationsCount;
                } else {
                    notificationCounter.classList.add('d-none');
                    document.getElementById('mark-all-as-read-btn').setAttribute('disabled', 'disabled');
                    document.getElementById('notification-items').innerHTML = 
                        '<li class="py-2 px-4 text-center text-muted">Aucune notification non lue</li>';
                }
                
                // Afficher un toast de confirmation
                showNotificationToast('Notification marquée comme lue');
            }
        })
        .catch(error => {
            console.error('Erreur lors du marquage de la notification comme lue:', error);
            showNotificationToast('Erreur lors du marquage de la notification', 'danger');
        });
    }
    
    // Fonction pour marquer toutes les notifications comme lues
    document.getElementById('mark-all-as-read-btn').addEventListener('click', function() {
        fetch('<?php echo e(url("/api/notifications/read-all")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Vider la liste des notifications
                document.getElementById('notification-items').innerHTML = 
                    '<li class="py-2 px-4 text-center text-muted">Aucune notification non lue</li>';
                
                // Mettre à jour le compteur
                unreadNotificationsCount = 0;
                const notificationCounter = document.getElementById('notification-counter');
                notificationCounter.classList.add('d-none');
                this.setAttribute('disabled', 'disabled');
                
                // Afficher un toast de confirmation
                showNotificationToast('Toutes les notifications ont été marquées comme lues');
            }
        })
        .catch(error => {
            console.error('Erreur lors du marquage de toutes les notifications comme lues:', error);
            showNotificationToast('Erreur lors du marquage des notifications', 'danger');
        });
    });
    
    // Fonction pour afficher un toast de notification
    function showNotificationToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
        
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
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', function() {
            toastContainer.removeChild(toast);
        });
    }
    
    // Charger les notifications au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        loadUnreadNotifications();
        
        // Rafraîchir les notifications toutes les 60 secondes
        setInterval(loadUnreadNotifications, 60000);
    });
</script><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/layouts/partials/notifications.blade.php ENDPATH**/ ?>
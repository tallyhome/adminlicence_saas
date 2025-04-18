/**
 * Système de notifications WebSocket pour AdminLicence
 * 
 * Ce fichier gère la connexion WebSocket et l'affichage des notifications
 * en temps réel pour les changements de statut des licences, les nouveaux tickets
 * de support et les paiements.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Initialisation de Laravel Echo pour les WebSockets
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: (process.env.MIX_PUSHER_SCHEME || 'https') === 'https',
    wsHost: process.env.MIX_PUSHER_HOST || window.location.hostname,
    wsPort: process.env.MIX_PUSHER_PORT || 6001,
    disableStats: true,
});

// Compteur de notifications non lues
let unreadNotificationsCount = 0;

// Classe pour gérer les notifications
class NotificationManager {
    constructor() {
        this.notifications = [];
        this.notificationContainer = document.getElementById('notification-container');
        this.notificationCounter = document.getElementById('notification-counter');
        this.notificationList = document.getElementById('notification-list');
        this.setupEventListeners();
    }

    // Initialiser les écouteurs d'événements
    setupEventListeners() {
        // Écouter les notifications privées pour l'administrateur connecté
        if (window.userId) {
            window.Echo.private(`App.Models.Admin.${window.userId}`)
                .notification((notification) => {
                    this.handleNewNotification(notification);
                });
        }

        // Écouter les notifications sur le canal général des licences
        window.Echo.channel('licence-status')
            .listen('LicenceStatusChanged', (event) => {
                this.handleLicenceStatusChange(event);
            });

        // Écouter les notifications sur le canal des tickets de support
        window.Echo.channel('support-tickets')
            .listen('NewSupportTicket', (event) => {
                this.handleNewSupportTicket(event);
            })
            .listen('SupportTicketStatusChanged', (event) => {
                this.handleSupportTicketStatusChange(event);
            });

        // Écouter les notifications sur le canal des paiements
        window.Echo.channel('payments')
            .listen('NewPayment', (event) => {
                this.handleNewPayment(event);
            });

        // Gestionnaire pour marquer les notifications comme lues
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('mark-as-read')) {
                e.preventDefault();
                const notificationId = e.target.dataset.id;
                this.markAsRead(notificationId);
            }
        });

        // Gestionnaire pour le bouton "Tout marquer comme lu"
        const markAllAsReadBtn = document.getElementById('mark-all-as-read');
        if (markAllAsReadBtn) {
            markAllAsReadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.markAllAsRead();
            });
        }
    }

    // Gérer une nouvelle notification
    handleNewNotification(notification) {
        this.notifications.unshift(notification);
        this.updateNotificationCounter();
        this.renderNotifications();
        this.showToast(notification.title, notification.message);
    }

    // Gérer un changement de statut de licence
    handleLicenceStatusChange(event) {
        const notification = {
            id: Date.now(),
            title: 'Changement de statut de licence',
            message: `La licence ${event.serial_key} a été ${event.status_text}`,
            time: event.timestamp,
            read: false,
            type: 'licence',
            data: event
        };
        this.handleNewNotification(notification);
    }

    // Gérer un nouveau ticket de support
    handleNewSupportTicket(event) {
        const notification = {
            id: Date.now(),
            title: 'Nouveau ticket de support',
            message: `Ticket #${event.ticket_id}: ${event.subject}`,
            time: event.timestamp,
            read: false,
            type: 'support',
            data: event
        };
        this.handleNewNotification(notification);
    }
    
    // Gérer un changement de statut de ticket de support
    handleSupportTicketStatusChange(event) {
        const statusMessages = {
            'open': 'ouvert',
            'in_progress': 'en cours',
            'waiting': 'en attente',
            'closed': 'fermé'
        };
        
        const statusText = statusMessages[event.status] || event.status;
        
        const notification = {
            id: Date.now(),
            title: 'Mise à jour de ticket',
            message: `Ticket #${event.ticket_id}: Statut changé à ${statusText}`,
            time: event.timestamp,
            read: false,
            type: 'support-update',
            data: event
        };
        this.handleNewNotification(notification);
    }

    // Gérer un nouveau paiement
    handleNewPayment(event) {
        const notification = {
            id: Date.now(),
            title: 'Nouveau paiement reçu',
            message: `Paiement de ${event.amount}€ pour ${event.client_name}`,
            time: event.timestamp,
            read: false,
            type: 'payment',
            data: event
        };
        this.handleNewNotification(notification);
    }

    // Mettre à jour le compteur de notifications
    updateNotificationCounter() {
        const unreadCount = this.notifications.filter(n => !n.read).length;
        unreadNotificationsCount = unreadCount;
        
        if (this.notificationCounter) {
            if (unreadCount > 0) {
                this.notificationCounter.textContent = unreadCount;
                this.notificationCounter.classList.remove('hidden');
                // Ajoute un badge rouge avec animation de pulse
                this.notificationCounter.classList.add('bg-red-500', 'text-white', 'rounded-full', 'px-2', 'py-1', 'text-xs', 'font-bold', 'animate-pulse');
            } else {
                this.notificationCounter.classList.add('hidden');
                // Retire le badge rouge
                this.notificationCounter.classList.remove('bg-red-500', 'text-white', 'rounded-full', 'px-2', 'py-1', 'text-xs', 'font-bold', 'animate-pulse');
            }
        }
    }

    // Afficher les notifications dans la liste déroulante
    renderNotifications() {
        if (!this.notificationList) return;

        // Vider la liste actuelle
        this.notificationList.innerHTML = '';

        if (this.notifications.length === 0) {
            const emptyItem = document.createElement('li');
            emptyItem.className = 'py-2 px-4 text-gray-500 text-center';
            emptyItem.textContent = 'Aucune notification';
            this.notificationList.appendChild(emptyItem);
            return;
        }

        // Ajouter chaque notification à la liste
        this.notifications.slice(0, 10).forEach(notification => {
            const item = document.createElement('li');
            item.className = notification.read ? 'py-2 px-4 border-b hover:bg-gray-50' : 'py-2 px-4 border-b hover:bg-gray-50 bg-blue-50';

            // Créer le contenu de la notification
            const content = document.createElement('div');
            
            // Titre avec icône selon le type
            const title = document.createElement('div');
            title.className = 'font-semibold flex items-center';
            
            // Ajouter l'icône appropriée selon le type
            let iconClass = '';
            switch (notification.type) {
                case 'licence':
                    iconClass = 'fas fa-key text-blue-500';
                    break;
                case 'support':
                    iconClass = 'fas fa-ticket-alt text-yellow-500';
                    break;
                case 'support-update':
                    iconClass = 'fas fa-exchange-alt text-orange-500';
                    break;
                case 'payment':
                    iconClass = 'fas fa-money-bill text-green-500';
                    break;
                default:
                    iconClass = 'fas fa-bell text-gray-500';
            }
            
            title.innerHTML = `<i class="${iconClass} mr-2"></i>${notification.title}`;
            content.appendChild(title);
            
            // Message
            const message = document.createElement('div');
            message.className = 'text-sm text-gray-600';
            message.textContent = notification.message;
            content.appendChild(message);
            
            // Horodatage
            const time = document.createElement('div');
            time.className = 'text-xs text-gray-400 mt-1';
            time.textContent = notification.time;
            content.appendChild(time);
            
            // Actions
            const actions = document.createElement('div');
            actions.className = 'mt-2 flex justify-between';
            
            // Bouton pour marquer comme lu
            if (!notification.read) {
                const markAsReadBtn = document.createElement('button');
                markAsReadBtn.className = 'mark-as-read text-xs text-blue-500 hover:underline';
                markAsReadBtn.textContent = 'Marquer comme lu';
                markAsReadBtn.dataset.id = notification.id;
                actions.appendChild(markAsReadBtn);
            }
            
            // Lien vers la ressource concernée
            const viewLink = document.createElement('a');
            viewLink.className = 'text-xs text-blue-500 hover:underline';
            viewLink.textContent = 'Voir les détails';
            
            // Définir l'URL selon le type de notification
            switch (notification.type) {
                case 'licence':
                    viewLink.href = `/admin/serial-keys/${notification.data.serial_key_id}`;
                    break;
                case 'support':
                    viewLink.href = `/admin/support-tickets/${notification.data.ticket_id}`;
                    break;
                case 'payment':
                    viewLink.href = `/admin/invoices/${notification.data.invoice_id}`;
                    break;
                default:
                    viewLink.href = '#';
            }
            
            actions.appendChild(viewLink);
            content.appendChild(actions);
            
            item.appendChild(content);
            this.notificationList.appendChild(item);
        });

        // Ajouter un lien pour voir toutes les notifications si nécessaire
        if (this.notifications.length > 10) {
            const viewAllItem = document.createElement('li');
            viewAllItem.className = 'py-2 px-4 text-center border-t';
            const viewAllLink = document.createElement('a');
            viewAllLink.href = '/admin/notifications';
            viewAllLink.className = 'text-blue-500 hover:underline text-sm';
            viewAllLink.textContent = 'Voir toutes les notifications';
            viewAllItem.appendChild(viewAllLink);
            this.notificationList.appendChild(viewAllItem);
        }
    }

    // Marquer une notification comme lue
    markAsRead(notificationId) {
        const index = this.notifications.findIndex(n => n.id.toString() === notificationId.toString());
        if (index !== -1) {
            this.notifications[index].read = true;
            this.updateNotificationCounter();
            this.renderNotifications();
            
            // Utiliser la nouvelle API dédiée pour marquer comme lu
            fetch(`/api/notifications/read/${notificationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Notification marquée comme lue:', data);
            })
            .catch(error => {
                console.error('Erreur lors du marquage de la notification:', error);
            });
        }
    }

    // Marquer toutes les notifications comme lues
    markAllAsRead() {
        this.notifications.forEach(notification => {
            notification.read = true;
        });
        this.updateNotificationCounter();
        this.renderNotifications();
        
        // Utiliser la nouvelle API dédiée pour marquer toutes comme lues
        fetch('/api/notifications/read-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Toutes les notifications marquées comme lues:', data);
        })
        .catch(error => {
            console.error('Erreur lors du marquage des notifications:', error);
        });
    }

    // Afficher une notification toast
    showToast(title, message) {
        // Vérifier si la div de toast existe déjà
        let toastContainer = document.getElementById('toast-container');
        
        // Créer le conteneur de toast s'il n'existe pas
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed bottom-4 right-4 z-50';
            document.body.appendChild(toastContainer);
        }
        
        // Créer le toast
        const toast = document.createElement('div');
        toast.className = 'bg-white border border-gray-200 rounded-lg shadow-lg p-4 mb-3 transform transition-all duration-300 translate-y-full opacity-0 flex items-start max-w-md';
        toast.innerHTML = `
            <div class="flex-shrink-0 text-blue-500 mr-3">
                <i class="fas fa-bell text-xl"></i>
            </div>
            <div class="flex-grow">
                <h4 class="font-semibold text-gray-800">${title}</h4>
                <p class="text-sm text-gray-600">${message}</p>
            </div>
            <button class="ml-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Ajouter le toast au conteneur
        toastContainer.appendChild(toast);
        
        // Animation d'entrée
        setTimeout(() => {
            toast.classList.remove('translate-y-full', 'opacity-0');
        }, 10);
        
        // Configurer la fermeture du toast
        const closeBtn = toast.querySelector('button');
        closeBtn.addEventListener('click', () => {
            closeToast(toast);
        });
        
        // Fermer automatiquement après 5 secondes
        setTimeout(() => {
            closeToast(toast);
        }, 5000);
        
        // Fonction pour fermer le toast avec animation
        function closeToast(toastElement) {
            toastElement.classList.add('opacity-0', 'translate-y-full');
            setTimeout(() => {
                toastElement.remove();
            }, 300);
        }
    }
}

// Initialiser le gestionnaire de notifications lorsque le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    const notificationManager = new NotificationManager();
    window.notificationManager = notificationManager;
});
@extends('admin.layouts.app')

@section('title', 'Notifications')

@php
use Illuminate\Support\Facades\Auth;
@endphp

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Notifications</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Notifications</li>
    </ol>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('solution-finale.marquer-tout-comme-lu') }}" class="btn btn-outline-primary me-2" {{ $notifications->where('read_at', null)->count() > 0 ? '' : 'disabled' }}>
                <i class="fas fa-check-double me-2"></i>Marquer tout comme lu
            </a>
        </div>
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Créer une notification
        </a>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des notifications</h6>
            <div class="d-flex gap-2">
                <div class="input-group">
                    <input type="text" id="searchNotifications" class="form-control form-control-sm" placeholder="Rechercher...">
                    <button class="btn btn-outline-secondary btn-sm" type="button" id="clearSearch">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <select id="filterNotifications" class="form-select form-select-sm">
                    <option value="all">Toutes les notifications</option>
                    <option value="unread">Non lues</option>
                    <option value="read">Lues</option>
                </select>
                <select id="filterImportance" class="form-select form-select-sm">
                    <option value="all">Toutes les priorités</option>
                    <option value="normal">Normal</option>
                    <option value="high">Important</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <div class="list-group list-group-flush" id="notificationsList">
                    @foreach($notifications as $notification)
                        @php
                            $importanceClass = '';
                            if (!$notification->read_at) {
                                if ($notification->importance === 'urgent') {
                                    $importanceClass = 'list-group-item-danger';
                                } elseif ($notification->importance === 'high') {
                                    $importanceClass = 'list-group-item-warning';
                                } else {
                                    $importanceClass = 'list-group-item-primary';
                                }
                            }
                        @endphp
                        <div class="list-group-item list-group-item-action {{ $importanceClass }}" id="notification-{{ $notification->id }}">
                            <div class="d-flex w-100 align-items-start">
                                @php
                                    $data = $notification->data;
                                    $iconClass = 'fas fa-bell';
                                    $iconColor = 'text-secondary';
                                    $title = 'Notification';
                                    
                                    // Définir l'icône et la couleur en fonction de l'importance
                                    if ($notification->importance === 'urgent') {
                                        $iconClass = 'fas fa-exclamation-triangle';
                                        $iconColor = 'text-danger';
                                        $title = 'Notification urgente';
                                    } elseif ($notification->importance === 'high') {
                                        $iconClass = 'fas fa-exclamation-circle';
                                        $iconColor = 'text-warning';
                                        $title = 'Notification importante';
                                    }
                                    
                                    if (isset($data['action'])) {
                                        $iconClass = 'fas fa-key';
                                        $iconColor = 'text-primary';
                                        $title = 'Changement de statut de licence';
                                    } elseif (isset($data['ticket_id'])) {
                                        $iconClass = 'fas fa-ticket-alt';
                                        $iconColor = 'text-warning';
                                        $title = 'Nouveau ticket de support';
                                    } elseif (isset($data['invoice_id'])) {
                                        $iconClass = 'fas fa-money-bill';
                                        $iconColor = 'text-success';
                                        $title = 'Nouveau paiement';
                                    }
                                @endphp
                                
                                <div class="me-3">
                                    <span class="notification-icon rounded-circle d-flex justify-content-center align-items-center">
                                        <i class="{{ $iconClass }} {{ $iconColor }} fa-lg"></i>
                                    </span>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">
                                            {{ $title }}
                                            @if($notification->importance === 'urgent')
                                                <span class="badge bg-danger ms-2">Urgent</span>
                                            @elseif($notification->importance === 'high')
                                                <span class="badge bg-warning text-dark ms-2">Important</span>
                                            @endif
                                        </h5>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    
                                    @if(isset($data['action']))
                                    <p class="text-muted mt-1">
                                        @php
                                            $statusMessages = [
                                                'revoked' => 'révoquée',
                                                'suspended' => 'suspendue',
                                                'expired' => 'expirée',
                                                'activated' => 'activée',
                                                'renewed' => 'renouvelée'
                                            ];
                                            $status = $statusMessages[$data['action']] ?? $data['action'];
                                        @endphp
                                        La licence <span class="font-medium">{{ $data['serial_key'] ?? 'N/A' }}</span> a été {{ $status }}.
                                    </p>
                                    <div class="mt-2 d-flex gap-3">
                                        @if(!$notification->read_at)
                                            <a href="{{ route('solution-finale.marquer-comme-lu', $notification->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-check me-1"></i>Marquer comme lu
                                        </a>
                                        @endif
                                        <a href="{{ route('admin.serial-keys.show', $data['serial_key_id']) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye me-1"></i>Voir les détails
                                        </a>
                                    </div>
                                @elseif(isset($data['ticket_id']))
                                    <p class="text-muted mt-1">
                                        Nouveau ticket #{{ $data['ticket_id'] }}: {{ $data['subject'] ?? 'Sans sujet' }}
                                    </p>
                                    <div class="mt-2 d-flex gap-3">
                                        @if(!$notification->read_at)
                                            <a href="{{ route('solution-finale.marquer-comme-lu', $notification->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-check me-1"></i>Marquer comme lu
                                        </a>
                                        @endif
                                        <a href="{{ route('admin.support-tickets.show', $data['ticket_id']) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye me-1"></i>Voir le ticket
                                        </a>
                                    </div>
                                @elseif(isset($data['invoice_id']))
                                    <p class="text-muted mt-1">
                                        Paiement de {{ $data['amount'] ?? '0' }}€ reçu de {{ $data['client_name'] ?? 'Client' }}
                                    </p>
                                    <div class="mt-2 d-flex gap-3">
                                        @if(!$notification->read_at)
                                            <a href="{{ route('solution-finale.marquer-comme-lu', $notification->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-check me-1"></i>Marquer comme lu
                                        </a>
                                        @endif
                                        <a href="{{ route('admin.invoices.show', $data['invoice_id']) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye me-1"></i>Voir la facture
                                        </a>
                                    </div>
                                @else
                                    <p class="text-muted mt-1">
                                        @if(isset($data['message']))
                                            {{ $data['message'] }}
                                        @elseif(is_string($notification->data))
                                            {{ $notification->data }}
                                        @elseif(is_array($notification->data) && !empty($notification->data))
                                            {{ json_encode($notification->data) }}
                                        @else
                                            {{ $notification->message ?? 'Aucun détail disponible' }}
                                        @endif
                                    </p>
                                    <div class="mt-2">
                                        @if(!$notification->read_at)
                                            <a href="{{ route('solution-finale.marquer-comme-lu', $notification->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-check me-1"></i>Marquer comme lu
                                        </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    {{ $notifications->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <p class="text-muted">Vous n'avez aucune notification pour le moment.</p>
            </div>
        @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Pas besoin de JavaScript pour la solution finale -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Marquer une notification comme lue
        document.querySelectorAll('.mark-as-read').forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.dataset.id;
                const notificationItem = document.getElementById('notification-' + notificationId);
                
                const url = this.dataset.url || `{{ url('/api/notifications/read/__ID__') }}`.replace('__ID__', notificationId);
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Changer l'apparence de la notification
                        notificationItem.classList.remove('list-group-item-primary');
                        this.remove();
                        
                        // Mettre à jour le compteur de notifications non lues
                        const unreadCount = document.querySelectorAll('.list-group-item-primary').length;
                        if (unreadCount === 0) {
                            document.getElementById('markAllAsReadBtn').setAttribute('disabled', 'disabled');
                        }
                        
                        // Afficher un toast de confirmation
                        showToast('Notification marquée comme lue');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Une erreur est survenue', 'danger');
                });
            });
        });

        // Marquer toutes les notifications comme lues
        document.getElementById('markAllAsReadBtn').addEventListener('click', function() {
            fetch('{{ url("/api/notifications/read-all") }}', {
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
                    // Mettre à jour l'interface sans rechargement
                    document.querySelectorAll('.list-group-item-primary').forEach(item => {
                        item.classList.remove('list-group-item-primary');
                    });
                    document.querySelectorAll('.mark-as-read').forEach(btn => {
                        btn.remove();
                    });
                    this.setAttribute('disabled', 'disabled');
                    
                    // Afficher un toast de confirmation
                    showToast('Toutes les notifications ont été marquées comme lues');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showToast('Une erreur est survenue', 'danger');
            });
        });
        
        // Fonction pour filtrer les notifications
        function filterNotifications() {
            const readFilter = document.getElementById('filterNotifications').value;
            const importanceFilter = document.getElementById('filterImportance').value;
            const searchText = document.getElementById('searchNotifications').value.toLowerCase();
            const notifications = document.querySelectorAll('#notificationsList .list-group-item');
            
            notifications.forEach(item => {
                // Filtre par statut de lecture
                let showByReadStatus = true;
                if (readFilter === 'unread') {
                    showByReadStatus = item.classList.contains('list-group-item-primary') || 
                                      item.classList.contains('list-group-item-warning') || 
                                      item.classList.contains('list-group-item-danger');
                } else if (readFilter === 'read') {
                    showByReadStatus = !item.classList.contains('list-group-item-primary') && 
                                      !item.classList.contains('list-group-item-warning') && 
                                      !item.classList.contains('list-group-item-danger');
                }
                
                // Filtre par importance
                let showByImportance = true;
                if (importanceFilter !== 'all') {
                    const importanceBadge = item.querySelector('.badge');
                    if (importanceFilter === 'urgent') {
                        showByImportance = importanceBadge && importanceBadge.classList.contains('bg-danger');
                    } else if (importanceFilter === 'high') {
                        showByImportance = importanceBadge && importanceBadge.classList.contains('bg-warning');
                    } else if (importanceFilter === 'normal') {
                        showByImportance = !importanceBadge;
                    }
                }
                
                // Filtre par texte de recherche
                let showBySearch = true;
                if (searchText) {
                    const title = item.querySelector('h5').textContent.toLowerCase();
                    const content = item.querySelector('p').textContent.toLowerCase();
                    showBySearch = title.includes(searchText) || content.includes(searchText);
                }
                
                // Afficher ou masquer l'élément
                item.style.display = (showByReadStatus && showByImportance && showBySearch) ? 'block' : 'none';
            });
        }
        
        // Écouteurs d'événements pour les filtres
        document.getElementById('filterNotifications').addEventListener('change', filterNotifications);
        document.getElementById('filterImportance').addEventListener('change', filterNotifications);
        document.getElementById('searchNotifications').addEventListener('input', filterNotifications);
        
        // Bouton pour effacer la recherche
        document.getElementById('clearSearch').addEventListener('click', function() {
            document.getElementById('searchNotifications').value = '';
            filterNotifications();
        });
        
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
    });
</script>
@endpush
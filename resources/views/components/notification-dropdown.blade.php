<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <!-- Icône de notification avec compteur -->
    <button @click="open = !open" class="relative p-1 text-gray-600 hover:text-gray-800 focus:outline-none">
        <i class="fas fa-bell text-xl"></i>
        <span id="notification-counter" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full {{ count(auth()->user()->unreadNotifications) ? '' : 'hidden' }}">
            {{ count(auth()->user()->unreadNotifications) }}
        </span>
    </button>

    <!-- Menu déroulant des notifications -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 transform scale-95" 
         x-transition:enter-end="opacity-100 transform scale-100" 
         x-transition:leave="transition ease-in duration-150" 
         x-transition:leave-start="opacity-100 transform scale-100" 
         x-transition:leave-end="opacity-0 transform scale-95" 
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg overflow-hidden z-50"
         style="display: none;">
        
        <!-- En-tête du menu -->
        <div class="px-4 py-3 border-b flex justify-between items-center bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
            <button id="mark-all-as-read" class="text-xs text-blue-500 hover:underline focus:outline-none">
                Tout marquer comme lu
            </button>
        </div>
        
        <!-- Liste des notifications -->
        <div class="max-h-96 overflow-y-auto" id="notification-list">
            @if(count(auth()->user()->notifications) > 0)
                @foreach(auth()->user()->notifications->take(5) as $notification)
                    <div class="py-2 px-4 border-b hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-blue-50' }}">
                        @php
                            $data = $notification->data;
                            $iconClass = 'fas fa-bell text-gray-500';
                            $title = 'Notification';
                            
                            if (isset($data['action'])) {
                                $iconClass = 'fas fa-key text-blue-500';
                                $title = 'Changement de statut de licence';
                            } elseif (isset($data['ticket_id'])) {
                                $iconClass = 'fas fa-ticket-alt text-yellow-500';
                                $title = 'Nouveau ticket de support';
                            } elseif (isset($data['invoice_id'])) {
                                $iconClass = 'fas fa-money-bill text-green-500';
                                $title = 'Nouveau paiement';
                            }
                        @endphp
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-3">
                                <i class="{{ $iconClass }}"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="font-semibold text-sm">{{ $title }}</div>
                                <div class="text-xs text-gray-600">
                                    @if(isset($data['action']))
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
                                        La licence {{ $data['serial_key'] ?? 'N/A' }} a été {{ $status }}.
                                    @elseif(isset($data['ticket_id']))
                                        Nouveau ticket: {{ $data['subject'] ?? 'Sans sujet' }}
                                    @elseif(isset($data['invoice_id']))
                                        Paiement de {{ $data['amount'] ?? '0' }}€ reçu
                                    @else
                                        {{ $data['message'] ?? 'Aucun détail disponible' }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </div>
                                <div class="mt-2 flex justify-between">
                                    @if(!$notification->read_at)
                                        <button class="mark-as-read text-xs text-blue-500 hover:underline" data-id="{{ $notification->id }}">
                                            Marquer comme lu
                                        </button>
                                    @endif
                                    
                                    @if(isset($data['action']))
                                        <a href="{{ route('admin.serial-keys.show', $data['serial_key_id']) }}" class="text-xs text-blue-500 hover:underline">
                                            Voir les détails
                                        </a>
                                    @elseif(isset($data['ticket_id']))
                                        <a href="{{ route('admin.support-tickets.show', $data['ticket_id']) }}" class="text-xs text-blue-500 hover:underline">
                                            Voir le ticket
                                        </a>
                                    @elseif(isset($data['invoice_id']))
                                        <a href="{{ route('admin.invoices.show', $data['invoice_id']) }}" class="text-xs text-blue-500 hover:underline">
                                            Voir la facture
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="py-4 px-4 text-center text-gray-500">
                    Aucune notification
                </div>
            @endif
        </div>
        
        <!-- Pied du menu -->
        @if(count(auth()->user()->notifications) > 5)
            <div class="px-4 py-2 border-t text-center">
                <a href="{{ route('admin.notifications.index') }}" class="text-sm text-blue-500 hover:underline">
                    Voir toutes les notifications
                </a>
            </div>
        @endif
    </div>
</div>
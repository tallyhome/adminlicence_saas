<?php $__env->startSection('title', 'Notifications'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Notifications</h1>
        <?php if(auth('admin')->user() && auth('admin')->user()->is_super_admin): ?>
            <a href="<?php echo e(route('admin.notifications.create')); ?>" class="btn btn-warning me-2">
                <i class="fas fa-plus"></i> Créer une notification
            </a>
        <?php endif; ?>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if($notifications->count() > 0): ?>
            <div class="divide-y">
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 hover:bg-gray-50 <?php echo e($notification->read_at ? '' : 'bg-blue-50'); ?>">
                        <div class="flex items-start">
                            <?php
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
                            ?>
                            
                            <div class="flex-shrink-0 mr-4">
                                <i class="<?php echo e($iconClass); ?> text-xl"></i>
                            </div>
                            
                            <div class="flex-grow">
                                <div class="flex justify-between">
                                    <h4 class="font-semibold text-gray-800"><?php echo e($title); ?></h4>
                                    <span class="text-sm text-gray-500"><?php echo e($notification->created_at->diffForHumans()); ?></span>
                                </div>
                                
                                <?php if(isset($data['action'])): ?>
                                    <p class="text-gray-600 mt-1">
                                        <?php
                                            $statusMessages = [
                                                'revoked' => 'révoquée',
                                                'suspended' => 'suspendue',
                                                'expired' => 'expirée',
                                                'activated' => 'activée',
                                                'renewed' => 'renouvelée'
                                            ];
                                            $status = $statusMessages[$data['action']] ?? $data['action'];
                                        ?>
                                        La licence <span class="font-medium"><?php echo e($data['serial_key'] ?? 'N/A'); ?></span> a été <?php echo e($status); ?>.
                                    </p>
                                    <div class="mt-2 flex space-x-4">
                                        <?php if(!$notification->read_at): ?>
                                            <button class="mark-as-read text-sm text-blue-500 hover:underline" data-id="<?php echo e($notification->id); ?>">
                                                Marquer comme lu
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('admin.serial-keys.show', $data['serial_key_id'])); ?>" class="text-sm text-blue-500 hover:underline">
                                            Voir les détails
                                        </a>
                                    </div>
                                <?php elseif(isset($data['ticket_id'])): ?>
                                    <p class="text-gray-600 mt-1">
                                        Nouveau ticket #<?php echo e($data['ticket_id']); ?>: <?php echo e($data['subject'] ?? 'Sans sujet'); ?>

                                    </p>
                                    <div class="mt-2 flex space-x-4">
                                        <?php if(!$notification->read_at): ?>
                                            <button class="mark-as-read text-sm text-blue-500 hover:underline" data-id="<?php echo e($notification->id); ?>">
                                                Marquer comme lu
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('admin.support-tickets.show', $data['ticket_id'])); ?>" class="text-sm text-blue-500 hover:underline">
                                            Voir le ticket
                                        </a>
                                    </div>
                                <?php elseif(isset($data['invoice_id'])): ?>
                                    <p class="text-gray-600 mt-1">
                                        Paiement de <?php echo e($data['amount'] ?? '0'); ?>€ reçu de <?php echo e($data['client_name'] ?? 'Client'); ?>

                                    </p>
                                    <div class="mt-2 flex space-x-4">
                                        <?php if(!$notification->read_at): ?>
                                            <button class="mark-as-read text-sm text-blue-500 hover:underline" data-id="<?php echo e($notification->id); ?>">
                                                Marquer comme lu
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('admin.invoices.show', $data['invoice_id'])); ?>" class="text-sm text-blue-500 hover:underline">
                                            Voir la facture
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <p class="text-gray-600 mt-1">
                                        <?php echo e($data['message'] ?? 'Aucun détail disponible'); ?>

                                    </p>
                                    <div class="mt-2">
                                        <?php if(!$notification->read_at): ?>
                                            <button class="mark-as-read text-sm text-blue-500 hover:underline" data-id="<?php echo e($notification->id); ?>">
                                                Marquer comme lu
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <div class="px-4 py-3 border-t">
                <?php echo e($notifications->links()); ?>

            </div>
        <?php else: ?>
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-bell-slash text-4xl mb-4"></i>
                <p>Vous n'avez aucune notification pour le moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Marquer une notification comme lue
        document.querySelectorAll('.mark-as-read').forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.dataset.id;
                fetch(`/admin/notifications/${notificationId}/read`, {
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
                        this.closest('.bg-blue-50').classList.remove('bg-blue-50');
                        this.remove();
                    }
                });
            });
        });

        // Marquer toutes les notifications comme lues
        document.getElementById('mark-all-as-read').addEventListener('click', function() {
            fetch('/admin/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Rafraîchir la page pour montrer les changements
                    window.location.reload();
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/notifications/index.blade.php ENDPATH**/ ?>
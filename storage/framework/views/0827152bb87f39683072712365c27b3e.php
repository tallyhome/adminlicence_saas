



<?php $__env->startSection('title', $pageTitle); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mt-4">
        <ol class="breadcrumb">
            <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($loop->last): ?>
                    <li class="breadcrumb-item active"><?php echo e($breadcrumb['name']); ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item"><a href="<?php echo e($breadcrumb['url']); ?>"><?php echo e($breadcrumb['name']); ?></a></li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ol>
    </nav>

    <h1 class="mt-4"><?php echo e($pageTitle); ?></h1>
    
    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form action="<?php echo e(route('admin.mail.providers.rapidmail.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-3">
                    <label for="api_key" class="form-label">Clé API</label>
                    <input type="text" class="form-control" id="api_key" name="api_key" value="<?php echo e(old('api_key', $config->api_key ?? '')); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo e(old('username', $config->username ?? '')); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="from_email" class="form-label">Email d'expédition</label>
                    <input type="email" class="form-control" id="from_email" name="from_email" value="<?php echo e(old('from_email', $config->from_email ?? '')); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="from_name" class="form-label">Nom d'expéditeur</label>
                    <input type="text" class="form-control" id="from_name" name="from_name" value="<?php echo e(old('from_name', $config->from_name ?? '')); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <button type="button" class="btn btn-secondary" onclick="testConnection()">Tester la connexion</button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-list me-1"></i>
            Listes de destinataires
        </div>
        <div class="card-body">
            <div id="lists-container">
                <!-- Les listes seront chargées ici dynamiquement -->
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-envelope me-1"></i>
            Mailings
        </div>
        <div class="card-body">
            <div id="mailings-container">
                <!-- Les mailings seront chargés ici dynamiquement -->
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function testConnection() {
    fetch('<?php echo e(route("admin.mail.providers.rapidmail.test")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Erreur : ' + data.error);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Erreur lors du test de connexion');
    });
}

function loadLists() {
    fetch('<?php echo e(route("admin.mail.providers.rapidmail.lists")); ?>')
    .then(response => response.text())
    .then(html => {
        document.getElementById('lists-container').innerHTML = html;
    })
    .catch(error => {
        console.error('Erreur lors du chargement des listes');
    });
}

function loadMailings() {
    fetch('<?php echo e(route("admin.mail.providers.rapidmail.mailings")); ?>')
    .then(response => response.text())
    .then(html => {
        document.getElementById('mailings-container').innerHTML = html;
    })
    .catch(error => {
        console.error('Erreur lors du chargement des mailings');
    });
}

// Charger les listes et mailings au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadLists();
    loadMailings();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence\resources\views/admin/mail/providers/rapidmail/index.blade.php ENDPATH**/ ?>
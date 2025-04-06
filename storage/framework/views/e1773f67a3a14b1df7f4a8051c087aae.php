

<?php $__env->startSection('title', 'Test Google2FA'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Test de Google2FA</h1>
        <a href="<?php echo e(route('admin.settings.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux paramètres
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Informations Google2FA</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Clé secrète générée</h5>
                        <div class="input-group">
                            <input type="text" class="form-control" value="<?php echo e($secret); ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copySecret()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h5>URL du QR Code</h5>
                        <textarea class="form-control" rows="3" readonly><?php echo e($qrCodeUrl); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <h5>QR Code</h5>
                        <div class="text-center p-3 bg-light rounded">
                            <img src="<?php echo e($googleChartUrl); ?>" alt="QR Code" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Vérification du code</h3>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        Scannez le QR code avec votre application d'authentification (comme Google Authenticator, Authy, etc.),
                        puis entrez le code généré ci-dessous pour vérifier qu'il fonctionne correctement.
                    </p>

                    <form id="verify-form">
                        <input type="hidden" id="secret" value="<?php echo e($secret); ?>">
                        <div class="mb-3">
                            <label for="code" class="form-label">Code d'authentification</label>
                            <input type="text" id="code" class="form-control" maxlength="6" placeholder="123456" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle"></i> Vérifier le code
                        </button>
                    </form>

                    <div id="result" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copySecret() {
    const secretInput = document.querySelector('input[value="<?php echo e($secret); ?>"]');
    secretInput.select();
    document.execCommand('copy');
    alert('Clé secrète copiée dans le presse-papiers');
}

document.getElementById('verify-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const secret = document.getElementById('secret').value;
    const code = document.getElementById('code').value;
    const resultDiv = document.getElementById('result');
    
    // Appel AJAX pour vérifier le code
    fetch('<?php echo e(route("admin.settings.verify-code")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ secret, code })
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.style.display = 'block';
        if (data.valid) {
            resultDiv.className = 'alert alert-success';
            resultDiv.innerHTML = '<i class="fas fa-check-circle"></i> Le code est valide! L\'authentification à deux facteurs fonctionne correctement.';
        } else {
            resultDiv.className = 'alert alert-danger';
            resultDiv.innerHTML = '<i class="fas fa-times-circle"></i> Le code est invalide. Veuillez réessayer.';
        }
    })
    .catch(error => {
        resultDiv.style.display = 'block';
        resultDiv.className = 'alert alert-danger';
        resultDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Une erreur est survenue: ' + error;
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence\resources\views/admin/settings/test-google2fa.blade.php ENDPATH**/ ?>
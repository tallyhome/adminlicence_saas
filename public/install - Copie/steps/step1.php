<div class="step-content">
    <h2><?php echo $translations['license_verification']; ?></h2>
    <p><?php echo $translations['license_verification_info']; ?></p>
    
    <div id="license-result" class="alert d-none"></div>
    
    <?php if (isset($_SESSION['license_verified']) && $_SESSION['license_verified']): ?>
    <div class="alert alert-success">
        <?php echo isset($translations['license_already_verified']) ? $translations['license_already_verified'] : 'Licence déjà vérifiée'; ?>
    </div>
    <?php endif; ?>
    
    <form id="license-form" method="post" action="ajax/verify_license.php">
        <div class="mb-3">
            <label for="license_key" class="form-label"><?php echo $translations['license_key']; ?></label>
            <input type="text" class="form-control" id="license_key" name="license_key" required 
                pattern="[A-Za-z0-9]{4,5}(-[A-Za-z0-9]{4,5}){3,}"
                placeholder="xxxx-xxxx-xxxx-xxxx" 
                value="<?php echo isset($_SESSION['license_key']) ? htmlspecialchars($_SESSION['license_key']) : ''; ?>">
            <div class="form-text"><?php echo $translations['license_key_info']; ?></div>
            <div class="invalid-feedback">
                <?php echo isset($translations['invalid_license_format']) ? $translations['invalid_license_format'] : 'Format de licence invalide. Format attendu: XXXX-XXXX-XXXX-XXXX'; ?>
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <button type="submit" id="verify-license" class="btn btn-primary">
                <span class="spinner-border spinner-border-sm d-none" id="license-spinner" role="status" aria-hidden="true"></span>
                <span id="verify-text"><?php echo $translations['verify']; ?></span>
            </button>
            
            <a href="?step=2" id="next-step" class="btn btn-success <?php echo isset($_SESSION['license_verified']) && $_SESSION['license_verified'] ? '' : 'disabled'; ?>">
                <?php echo $translations['next']; ?>
            </a>
        </div>
    </form>
    
    <div id="license-details" class="mt-4 <?php echo isset($_SESSION['license_verified']) && $_SESSION['license_verified'] ? '' : 'd-none'; ?>">
        <h3><?php echo $translations['license_details']; ?></h3>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th><?php echo $translations['status']; ?></th>
                        <td id="license-status"><?php echo isset($_SESSION['license_verified']) && $_SESSION['license_verified'] ? 'Valide' : 'Non vérifié'; ?></td>
                    </tr>
                    <?php if (isset($_SESSION['license_details']) && isset($_SESSION['license_details']['expiry_date'])): ?>
                    <tr>
                        <th><?php echo $translations['expiry_date']; ?></th>
                        <td id="license-expiry"><?php echo $_SESSION['license_details']['expiry_date']; ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['license_details']) && isset($_SESSION['license_details']['secure_code'])): ?>
                    <tr>
                        <th><?php echo $translations['secure_code']; ?></th>
                        <td id="license-secure-code"><?php echo $_SESSION['license_details']['secure_code']; ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

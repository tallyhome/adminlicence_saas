/**
 * Installation Wizard JavaScript
 */

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Fonction pour vérifier la clé de licence
    function verifyLicense(licenseKey) {
        const licenseInput = $('#license_key');
        
        // Vérifier si la clé de licence est vide
        if (!licenseKey) {
            licenseInput.addClass('is-invalid');
            showAlert('error', 'Veuillez entrer une clé de licence');
            return false;
        }
        
        // Vérifier le format de la clé de licence (XXXX-XXXX-XXXX-XXXX)
        const licensePattern = /^[A-Za-z0-9]{4,5}(-[A-Za-z0-9]{4,5}){3,}$/;
        if (!licensePattern.test(licenseKey)) {
            licenseInput.addClass('is-invalid');
            showAlert('error', 'Format de licence invalide. Format attendu: XXXX-XXXX-XXXX-XXXX');
            return false;
        }
        
        // Réinitialiser l'état de validation
        licenseInput.removeClass('is-invalid').addClass('is-valid');
        
        // Désactiver le bouton et afficher l'état de chargement
        const $button = $('#verify-license');
        $button.prop('disabled', true);
        $('#license-spinner').removeClass('d-none');
        $('#verify-text').text('Vérification...');
        
        // Masquer les alertes précédentes
        $('#license-result').addClass('d-none');
        
        // Envoyer la requête AJAX pour vérifier la licence
        $.ajax({
            url: 'ajax/verify_license.php',
            type: 'POST',
            data: { license_key: licenseKey },
            dataType: 'json',
            cache: false, // Désactiver le cache
            timeout: 30000, // 30 secondes de timeout
            success: function(response) {
                console.log('Réponse reçue:', response);
                
                // Réinitialiser l'état du bouton
                $button.prop('disabled', false);
                $('#license-spinner').addClass('d-none');
                $('#verify-text').text('Vérifier');
                
                // Gérer la réponse
                if (response && response.status === true) {
                    // Licence valide
                    showAlert('success', 'Licence valide !');
                    $('#license-details').removeClass('d-none');
                    $('#license-status').text('Valide');
                    $('#license-expiry').text(response.expiry_date || 'N/A');
                    $('#next-step').removeClass('disabled');
                    
                    // Mettre à jour les détails de la licence si disponibles
                    if (response.secure_code) {
                        $('#license-secure-code').text(response.secure_code);
                    }
                    
                    return true;
                } else {
                    // Licence invalide
                    showAlert('error', response.message || 'Clé de licence invalide');
                    $('#license-details').addClass('d-none');
                    $('#next-step').addClass('disabled');
                    
                    return false;
                }
            },
            error: function(xhr, status, error) {
                console.log('Erreur AJAX:', status, error);
                
                // Réinitialiser l'état du bouton
                $button.prop('disabled', false);
                $('#license-spinner').addClass('d-none');
                $('#verify-text').text('Vérifier');
                
                // Afficher le message d'erreur
                let errorMessage = 'Une erreur est survenue lors de la vérification. Veuillez réessayer.';
                
                if (status === 'timeout') {
                    errorMessage = 'La vérification a pris trop de temps. Veuillez réessayer.';
                } else if (xhr.responseText) {
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMessage = errorResponse.message;
                        }
                    } catch (e) {
                        console.error('Erreur lors de l\'analyse de la réponse:', e);
                    }
                }
                
                showAlert('error', errorMessage);
                console.error('Erreur de vérification de licence:', error, status, xhr);
                
                return false;
            }
        });
    }
    
    // Gérer le clic sur le bouton de vérification
    $('#verify-license').on('click', function() {
        const licenseKey = $('#license_key').val();
        verifyLicense(licenseKey);
    });
    
    // Gérer les changements dans le champ de licence
    $('#license_key').on('input', function() {
        // Réinitialiser l'état de validation
        $(this).removeClass('is-invalid is-valid');
        
        // Masquer les alertes précédentes
        $('#license-result').addClass('d-none');
        
        // Désactiver le bouton suivant
        $('#next-step').addClass('disabled');
    });
    
    // Gérer la soumission du formulaire de licence
    $('#license-form').on('submit', function(e) {
        e.preventDefault();
        const licenseKey = $('#license_key').val();
        verifyLicense(licenseKey);
    });
    
    // Function to show alerts
    function showAlert(type, message) {
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'success' ? 'alert-success' : 
                          type === 'warning' ? 'alert-warning' : 'alert-info';
        
        $('#license-result')
            .removeClass('d-none alert-danger alert-success alert-warning alert-info')
            .addClass(alertClass)
            .html(message);
    }
    
    // Handle language switching
    $('.language-switcher a').on('click', function(e) {
        // Already handled by href, this is just for visual feedback
        $('.language-switcher a').removeClass('active');
        $(this).addClass('active');
    });
    
    // Automatically show alerts for 5 seconds then fade out
    setTimeout(function() {
        $('.alert:not(.persistent)').fadeOut('slow');
    }, 5000);
    
    // Password strength meter
    $('#admin_password').on('input', function() {
        const password = $(this).val();
        const strengthMeter = $('#password-strength');
        
        if (!strengthMeter.length) {
            return;
        }
        
        // Simple password strength calculation
        let strength = 0;
        
        // Length check
        if (password.length >= 8) {
            strength += 1;
        }
        
        // Contains lowercase
        if (/[a-z]/.test(password)) {
            strength += 1;
        }
        
        // Contains uppercase
        if (/[A-Z]/.test(password)) {
            strength += 1;
        }
        
        // Contains number
        if (/[0-9]/.test(password)) {
            strength += 1;
        }
        
        // Contains special character
        if (/[^a-zA-Z0-9]/.test(password)) {
            strength += 1;
        }
        
        // Update strength meter
        strengthMeter.removeClass('bg-danger bg-warning bg-info bg-success');
        
        if (strength === 0) {
            strengthMeter.css('width', '0%');
        } else if (strength === 1) {
            strengthMeter.addClass('bg-danger').css('width', '20%');
        } else if (strength === 2) {
            strengthMeter.addClass('bg-warning').css('width', '40%');
        } else if (strength === 3) {
            strengthMeter.addClass('bg-info').css('width', '60%');
        } else if (strength === 4) {
            strengthMeter.addClass('bg-success').css('width', '80%');
        } else {
            strengthMeter.addClass('bg-success').css('width', '100%');
        }
    });
    
    // Confirm password validation
    $('#confirm_password').on('input', function() {
        const password = $('#admin_password').val();
        const confirmPassword = $(this).val();
        
        if (password && confirmPassword) {
            if (password !== confirmPassword) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        }
    });
    
    // Form validation before submission
    $('form').on('submit', function(e) {
        const requiredFields = $(this).find('[required]');
        let isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
    });
});

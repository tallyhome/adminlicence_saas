/**
 * Script pour gérer le thème sombre
 */
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si le thème sombre est activé dans la session
    const darkModeEnabled = document.body.classList.contains('dark-mode');
    
    // Appliquer le thème sombre si activé
    if (darkModeEnabled) {
        document.body.classList.add('dark-mode');
    }
    
    // Écouter les changements de thème depuis la page des paramètres
    const darkModeToggle = document.getElementById('dark_mode');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', function() {
            if (this.checked) {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
            }
        });
    }
});
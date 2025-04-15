#!/bin/bash

# Définir le chemin racine
ROOT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"

# Vérifier si PHP est installé
if ! command -v php &> /dev/null; then
    echo "Erreur : PHP n'est pas installé"
    exit 1
fi

# Vérifier si Composer est installé
if ! command -v composer &> /dev/null; then
    echo "Erreur : Composer n'est pas installé"
    exit 1
fi

# Se déplacer dans le répertoire racine
cd "$ROOT_PATH" || exit 1

# Installer les dépendances
echo "Installation des dépendances..."
composer install --no-interaction --no-dev --optimize-autoloader

# Générer la clé d'application
echo "Génération de la clé d'application..."
php artisan key:generate

# Nettoyer le cache de configuration
echo "Nettoyage du cache..."
php artisan config:clear
php artisan cache:clear

# Exécuter les migrations
echo "Exécution des migrations..."
php artisan migrate --force

echo "Installation terminée avec succès !"
@echo off
setlocal enabledelayedexpansion

:: Définir le chemin racine
set "ROOT_PATH=%~dp0..\.."

:: Vérifier si PHP est installé
where php >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo Erreur : PHP n'est pas installe
    exit /b 1
)

:: Vérifier si Composer est installé
where composer >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo Erreur : Composer n'est pas installe
    exit /b 1
)

:: Se déplacer dans le répertoire racine
cd /d "%ROOT_PATH%"

:: Installer les dépendances
echo Installation des dependances...
call composer install --no-interaction --no-dev --optimize-autoloader

:: Générer la clé d'application
echo Generation de la cle d'application...
call php artisan key:generate

:: Nettoyer le cache de configuration
echo Nettoyage du cache...
call php artisan config:clear
call php artisan cache:clear

:: Exécuter les migrations
echo Execution des migrations...
call php artisan migrate --force

echo Installation terminee avec succes ! 
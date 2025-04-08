# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère à [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - 2024-04-07

### Ajouts

-   Système d'authentification à double facteur pour les administrateurs
-   Support multilingue complet (FR, EN, ZH, TR)
-   Nouveau système de gestion des projets avec statuts
-   Système de notifications par email avec templates personnalisables
-   Interface de gestion des clés API
-   Système de journalisation des actions
-   Documentation API intégrée

### Améliorations

-   Migration vers Laravel 12
-   Refonte complète de l'interface utilisateur
-   Optimisation des performances
-   Amélioration de la sécurité
-   Meilleure gestion des licences et des clés
-   Interface responsive améliorée

### Corrections de bugs

-   Correction des problèmes de validation des licences
-   Résolution des problèmes de cache
-   Correction des erreurs d'affichage
-   Correction des problèmes de timezone
-   Résolution des conflits de routes
-   Correction des erreurs de validation

## [1.9.5] - 06/04/2025

### Ajouts

-   Intégration de l'authentification à double facteur Google
-   Support multilingue complet (EN, FR, ZH, TR)
-   Nouveau système de documentation intégré avec DocumentationController
-   Documentation autonome accessible via /public/api-docs.php

### Améliorations

-   Migration vers Laravel 12
-   Optimisation du système de routage API
-   Interface de documentation plus intuitive
-   Amélioration de la sécurité avec 2FA

### Corrections de bugs

-   Correction des commandes artisan pour Laravel 12
-   Résolution du problème "Target class [files]"
-   Correction du problème package:discover
-   Mise en place d'une alternative à artisan serve avec serve.php

## [1.9.4] - 05/04/2025

### Ajouts

-   Système de notifications par email
-   Interface de gestion des clés API
-   Système de journalisation des actions
-   Support multilingue (FR, EN)

### Améliorations

-   Refonte de l'interface utilisateur
-   Optimisation des performances
-   Amélioration de la sécurité

### Corrections de bugs

-   Correction des problèmes de validation des licences
-   Résolution des problèmes de cache
-   Correction des erreurs d'affichage

## [1.9.3] - 04/04/2025

### Ajouts

-   Système de gestion des projets
-   Interface de configuration des emails
-   Système de backup automatique

### Améliorations

-   Optimisation de la base de données
-   Amélioration de l'API
-   Interface responsive

### Corrections de bugs

-   Correction des problèmes de timezone
-   Résolution des conflits de routes
-   Correction des erreurs de validation

## [1.9.2] - 03/04/2025

### Ajouts

-   Système de gestion des licences
-   Interface d'administration
-   API de vérification des licences

### Améliorations

-   Optimisation du code
-   Amélioration de la sécurité
-   Interface utilisateur améliorée

### Corrections de bugs

-   Correction des problèmes de cache
-   Résolution des erreurs de validation
-   Correction des problèmes d'affichage

## [1.9.1] - 02/04/2025

### Ajouts

-   Système de gestion des utilisateurs
-   Interface de configuration
-   Système de logs

### Améliorations

-   Optimisation des performances
-   Amélioration de la sécurité
-   Interface responsive

### Corrections de bugs

-   Correction des problèmes de timezone
-   Résolution des conflits de routes
-   Correction des erreurs de validation

## [1.9.0] - 01/04/2025

### Ajouts

-   Version initiale du système
-   Interface d'administration de base
-   API de base

### Améliorations

-   Optimisation initiale
-   Sécurité de base
-   Interface utilisateur de base

### Corrections de bugs

-   Corrections initiales
-   Résolution des problèmes de base
-   Correction des erreurs de base

## [1.8.0] - 05/04/2025

### Ajouts

-   Création de points d'entrée API directs pour la vérification des licences
-   Ajout d'une route API de test pour vérifier que l'API fonctionne correctement
-   Support des chemins d'API avec et sans préfixe v1 pour la compatibilité
-   Ajout de logs pour le débogage des appels API

### Améliorations

-   Amélioration de la gestion des erreurs dans l'API
-   Mise à jour de la documentation pour l'intégration de l'API dans les applications clientes
-   Création d'un outil de test pour vérifier le bon fonctionnement de l'API

## [1.7.0] - 05/04/2025

### Ajouts

-   Système de recherche avancé pour les clés de licence (recherche par clé, domaine, IP, projet)
-   Filtres pour afficher les clés par projet, statut, domaine et adresse IP
-   Ajout du statut "Expirée" dans les filtres de clés de licence
-   Possibilité de générer jusqu'à 100 000 clés de licence en une seule fois (augmentation de la limite précédente de 100)
-   Sélecteur de pagination amélioré avec options 10, 25, 50, 100, 500 et 1000 clés par page
-   Réorganisation de l'interface de gestion des clés pour une meilleure ergonomie

### Modifications

-   Déplacement du sélecteur de pagination sur le tableau de bord pour une meilleure ergonomie
-   Amélioration du calcul des "clés utilisées" sur le tableau de bord (une clé est considérée comme utilisée uniquement si elle a à la fois un domaine ET une adresse IP)
-   Optimisation de l'interface de gestion des clés de licence

### Corrections

-   Correction de l'affichage des icônes de pagination qui débordaient de l'écran

## [1.1.5] - Version précédente

## [2.0.0] - 2024-03-20

### Ajouts

-   Nouveau système de gestion des emails avec support multi-fournisseurs
    -   Intégration de PHPMail pour l'envoi via SMTP
    -   Intégration de Mailgun pour l'envoi via API
    -   Intégration de Mailchimp pour les campagnes marketing
    -   Intégration de Rapidmail comme alternative
-   Système de templates d'emails avec variables dynamiques
    -   Interface de gestion des templates
    -   Prévisualisation des templates
    -   Support multilingue pour les templates
-   Gestion des variables d'email
    -   Variables par défaut : {name}, {email}, {company}, {date}, {unsubscribe_link}
    -   Interface pour ajouter/modifier/supprimer des variables
    -   Validation des variables utilisées dans les templates

### Modifications

-   Refonte complète du menu de navigation
    -   Nouvelle section Email avec sous-menus pour chaque fournisseur
    -   Meilleure organisation des fonctionnalités
-   Amélioration de l'interface utilisateur
    -   Design plus moderne et cohérent
    -   Meilleure ergonomie des formulaires
    -   Ajout d'icônes pour une meilleure lisibilité

### Corrections

-   Correction des problèmes de routage pour les sections email
-   Amélioration de la gestion des erreurs
-   Optimisation des performances de chargement

### Sécurité

-   Validation renforcée des entrées utilisateur
-   Protection CSRF sur tous les formulaires
-   Sécurisation des clés API des fournisseurs d'email

### Base de données

-   Nouvelle table `email_variables` pour stocker les variables personnalisées
-   Mise à jour de la structure des templates d'email
-   Ajout des configurations pour les différents fournisseurs d'email

### Documentation

-   Mise à jour de la documentation API
-   Ajout de guides d'utilisation pour chaque fournisseur d'email
-   Documentation des variables disponibles pour les templates

### Notes de mise à jour

Pour mettre à jour vers cette version, exécutez :

```bash
php artisan migrate
php artisan optimize:clear
```

### Breaking Changes

-   La structure des templates d'email a été modifiée
-   Les anciennes configurations d'email doivent être migrées vers le nouveau système
-   Les routes des API d'email ont été restructurées

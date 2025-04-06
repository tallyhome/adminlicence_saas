# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère à [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.9.5] - 06/04/2025

### Ajouts
- Intégration de l'authentification à double facteur Google
- Support multilingue complet (EN, FR, ZH, TR)
- Nouveau système de documentation intégré avec DocumentationController
- Documentation autonome accessible via /public/api-docs.php

### Améliorations
- Migration vers Laravel 12
- Optimisation du système de routage API
- Interface de documentation plus intuitive
- Amélioration de la sécurité avec 2FA

### Corrections de bugs
- Correction des commandes artisan pour Laravel 12
- Résolution du problème "Target class [files]"
- Correction du problème package:discover
- Mise en place d'une alternative à artisan serve avec serve.php

## [1.9.4] - 05/04/2025

### Ajouts
- Système de notifications par email
- Interface de gestion des clés API
- Système de journalisation des actions
- Support multilingue (FR, EN)

### Améliorations
- Refonte de l'interface utilisateur
- Optimisation des performances
- Amélioration de la sécurité

### Corrections de bugs
- Correction des problèmes de validation des licences
- Résolution des problèmes de cache
- Correction des erreurs d'affichage

## [1.9.3] - 04/04/2025

### Ajouts
- Système de gestion des projets
- Interface de configuration des emails
- Système de backup automatique

### Améliorations
- Optimisation de la base de données
- Amélioration de l'API
- Interface responsive

### Corrections de bugs
- Correction des problèmes de timezone
- Résolution des conflits de routes
- Correction des erreurs de validation

## [1.9.2] - 03/04/2025

### Ajouts
- Système de gestion des licences
- Interface d'administration
- API de vérification des licences

### Améliorations
- Optimisation du code
- Amélioration de la sécurité
- Interface utilisateur améliorée

### Corrections de bugs
- Correction des problèmes de cache
- Résolution des erreurs de validation
- Correction des problèmes d'affichage

## [1.9.1] - 02/04/2025

### Ajouts
- Système de gestion des utilisateurs
- Interface de configuration
- Système de logs

### Améliorations
- Optimisation des performances
- Amélioration de la sécurité
- Interface responsive

### Corrections de bugs
- Correction des problèmes de timezone
- Résolution des conflits de routes
- Correction des erreurs de validation

## [1.9.0] - 01/04/2025

### Ajouts
- Version initiale du système
- Interface d'administration de base
- API de base

### Améliorations
- Optimisation initiale
- Sécurité de base
- Interface utilisateur de base

### Corrections de bugs
- Corrections initiales
- Résolution des problèmes de base
- Correction des erreurs de base

## [1.8.0] - 05/04/2025

### Ajouts
- Création de points d'entrée API directs pour la vérification des licences
- Ajout d'une route API de test pour vérifier que l'API fonctionne correctement
- Support des chemins d'API avec et sans préfixe v1 pour la compatibilité
- Ajout de logs pour le débogage des appels API

### Améliorations
- Amélioration de la gestion des erreurs dans l'API
- Mise à jour de la documentation pour l'intégration de l'API dans les applications clientes
- Création d'un outil de test pour vérifier le bon fonctionnement de l'API

## [1.7.0] - 05/04/2025

### Ajouts
- Système de recherche avancé pour les clés de licence (recherche par clé, domaine, IP, projet)
- Filtres pour afficher les clés par projet, statut, domaine et adresse IP
- Ajout du statut "Expirée" dans les filtres de clés de licence
- Possibilité de générer jusqu'à 100 000 clés de licence en une seule fois (augmentation de la limite précédente de 100)
- Sélecteur de pagination amélioré avec options 10, 25, 50, 100, 500 et 1000 clés par page
- Réorganisation de l'interface de gestion des clés pour une meilleure ergonomie

### Modifications
- Déplacement du sélecteur de pagination sur le tableau de bord pour une meilleure ergonomie
- Amélioration du calcul des "clés utilisées" sur le tableau de bord (une clé est considérée comme utilisée uniquement si elle a à la fois un domaine ET une adresse IP)
- Optimisation de l'interface de gestion des clés de licence

### Corrections
- Correction de l'affichage des icônes de pagination qui débordaient de l'écran

## [1.1.5] - Version précédente

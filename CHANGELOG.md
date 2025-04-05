# Changelog AdminLicence

Toutes les modifications notables apportées à ce projet seront documentées dans ce fichier.

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

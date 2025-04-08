# Documentation du Mode SaaS Multi-utilisateur

Ce document explique comment utiliser le système en mode SaaS multi-utilisateur, notamment comment se connecter avec différents rôles d'utilisateurs.

## Table des matières

1. [Introduction](#introduction)
2. [Rôles utilisateurs](#rôles-utilisateurs)
3. [Connexion au système](#connexion-au-système)
4. [Fonctionnalités par rôle](#fonctionnalités-par-rôle)
5. [Gestion des utilisateurs](#gestion-des-utilisateurs)

## Introduction

Le mode SaaS (Software as a Service) multi-utilisateur permet de gérer plusieurs clients et utilisateurs sur une seule instance de l'application. Chaque utilisateur dispose d'un rôle spécifique qui détermine ses droits d'accès et les fonctionnalités disponibles.

## Rôles utilisateurs

Le système comprend trois rôles principaux :

### SuperAdmin

Le SuperAdmin est l'administrateur global du système avec un accès complet à toutes les fonctionnalités.

- Gestion de tous les clients et projets
- Configuration globale du système
- Gestion des administrateurs
- Accès aux tickets de support transférés
- Supervision de toutes les licences

### Admin

L'Admin est responsable de la gestion d'un ensemble spécifique de clients et de projets.

- Gestion des clients assignés
- Création et gestion des licences
- Gestion des clés API
- Traitement des tickets de support
- Configuration des fournisseurs d'email

### Utilisateur

L'utilisateur standard a un accès limité aux fonctionnalités, généralement en tant que client final.

- Consultation de ses propres licences
- Création de tickets de support
- Mise à jour de son profil
- Consultation de la documentation

## Connexion au système

### Connexion SuperAdmin

1. Accédez à la page de connexion principale : `https://votre-domaine.com/login`
2. Entrez les identifiants SuperAdmin :
   - Email : l'email configuré pour le compte SuperAdmin (généralement défini lors de l'installation)
   - Mot de passe : le mot de passe du compte SuperAdmin
3. Si l'authentification à deux facteurs est activée, entrez le code généré par votre application d'authentification

Une fois connecté, vous serez redirigé vers le tableau de bord SuperAdmin avec toutes les fonctionnalités disponibles.

### Connexion Admin

1. Accédez à la page de connexion principale : `https://votre-domaine.com/login`
2. Entrez les identifiants Admin :
   - Email : l'email associé à votre compte Admin
   - Mot de passe : votre mot de passe Admin
3. Si l'authentification à deux facteurs est activée, entrez le code généré par votre application d'authentification

Une fois connecté, vous serez redirigé vers le tableau de bord Admin avec les fonctionnalités correspondant à votre rôle.

### Connexion Utilisateur

1. Accédez à la page de connexion principale : `https://votre-domaine.com/login`
2. Entrez les identifiants Utilisateur :
   - Email : l'email associé à votre compte Utilisateur
   - Mot de passe : votre mot de passe Utilisateur
3. Si l'authentification à deux facteurs est activée, entrez le code généré par votre application d'authentification

Une fois connecté, vous serez redirigé vers le tableau de bord Utilisateur avec un accès limité aux fonctionnalités.

## Fonctionnalités par rôle

### Fonctionnalités SuperAdmin

- **Gestion globale** : Accès à tous les clients, projets et licences dans le système
- **Configuration système** : Paramètres globaux, intégrations, fournisseurs d'email
- **Gestion des utilisateurs** : Création et gestion de tous les types d'utilisateurs
- **Support avancé** : Traitement des tickets escaladés par les administrateurs
- **Rapports** : Accès aux statistiques et rapports globaux

### Fonctionnalités Admin

- **Gestion des clients** : Création et gestion des clients assignés
- **Gestion des licences** : Création, modification et révocation des licences
- **Clés API** : Génération et gestion des clés API pour les intégrations
- **Support client** : Traitement des tickets de support de premier niveau
- **Rapports limités** : Accès aux statistiques concernant les clients assignés

### Fonctionnalités Utilisateur

- **Licences** : Consultation des licences attribuées
- **Support** : Création et suivi des tickets de support
- **Profil** : Mise à jour des informations personnelles
- **Documentation** : Accès aux guides d'utilisation

## Gestion des utilisateurs

### Création d'un compte SuperAdmin

Le compte SuperAdmin initial est généralement créé lors de l'installation du système. Pour créer des SuperAdmins supplémentaires :

1. Connectez-vous avec un compte SuperAdmin existant
2. Accédez à la section "Gestion des utilisateurs"
3. Cliquez sur "Ajouter un utilisateur"
4. Remplissez le formulaire avec les informations du nouvel utilisateur
5. Sélectionnez le rôle "SuperAdmin"
6. Cliquez sur "Enregistrer"

### Création d'un compte Admin

1. Connectez-vous avec un compte SuperAdmin
2. Accédez à la section "Gestion des utilisateurs"
3. Cliquez sur "Ajouter un utilisateur"
4. Remplissez le formulaire avec les informations du nouvel administrateur
5. Sélectionnez le rôle "Admin"
6. Assignez les clients que cet administrateur pourra gérer
7. Cliquez sur "Enregistrer"

### Création d'un compte Utilisateur

1. Connectez-vous avec un compte SuperAdmin ou Admin
2. Accédez à la section "Gestion des clients"
3. Sélectionnez le client pour lequel vous souhaitez créer un utilisateur
4. Cliquez sur "Ajouter un utilisateur"
5. Remplissez le formulaire avec les informations du nouvel utilisateur
6. Sélectionnez le rôle "Utilisateur"
7. Cliquez sur "Enregistrer"

Un email d'invitation sera envoyé à l'utilisateur avec les instructions pour définir son mot de passe et activer son compte.
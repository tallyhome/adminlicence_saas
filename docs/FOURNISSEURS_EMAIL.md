# Documentation des Fournisseurs d'Email

Ce document explique comment configurer et utiliser les différents fournisseurs d'email disponibles dans l'application.

## Table des matières

1. [Introduction](#introduction)
2. [SMTP](#smtp)
3. [PHPMail](#phpmail)
4. [Mailgun](#mailgun)
5. [Mailchimp](#mailchimp)
6. [Rapidmail](#rapidmail)
7. [Comparaison des fournisseurs](#comparaison-des-fournisseurs)
8. [Dépannage](#dépannage)

## Introduction

L'application prend en charge plusieurs fournisseurs d'email pour l'envoi de notifications, d'alertes et de communications aux utilisateurs. Chaque fournisseur a ses propres avantages, limites et exigences de configuration.

## SMTP

### Description
SMTP (Simple Mail Transfer Protocol) est la méthode standard pour envoyer des emails via Internet. C'est une solution fiable et universelle qui fonctionne avec la plupart des services d'email.

### Configuration requise
- Hôte SMTP (ex: smtp.gmail.com, smtp.office365.com)
- Port (généralement 587 pour TLS, 465 pour SSL)
- Nom d'utilisateur (souvent votre adresse email)
- Mot de passe
- Méthode de chiffrement (TLS/SSL)
- Adresse d'expéditeur
- Nom d'expéditeur

### Avantages
- Compatible avec presque tous les services d'email
- Contrôle total sur le processus d'envoi
- Pas de dépendance à des API tierces

### Inconvénients
- Configuration parfois complexe
- Peut nécessiter des ajustements de sécurité sur certains fournisseurs
- Limites d'envoi selon le fournisseur SMTP

### Exemple de configuration
```php
$config = [
    'host' => 'smtp.votredomaine.com',
    'port' => 587,
    'username' => 'email@votredomaine.com',
    'password' => 'votre-mot-de-passe',
    'encryption' => 'tls',
    'from_address' => 'noreply@votredomaine.com',
    'from_name' => 'Votre Application'
];
```

## PHPMail

### Description
PHPMail utilise la bibliothèque PHPMailer pour envoyer des emails. C'est une solution robuste qui offre plus de fonctionnalités que la fonction mail() native de PHP.

### Configuration requise
- Mêmes paramètres que SMTP (car PHPMailer utilise SMTP en arrière-plan)

### Avantages
- Gestion avancée des pièces jointes
- Support multilingue
- Meilleure gestion des erreurs que mail() natif

### Inconvénients
- Similaires à SMTP

### Exemple de configuration
```php
$config = [
    'host' => 'smtp.votredomaine.com',
    'port' => 587,
    'username' => 'email@votredomaine.com',
    'password' => 'votre-mot-de-passe',
    'encryption' => 'tls',
    'from_address' => 'noreply@votredomaine.com',
    'from_name' => 'Votre Application'
];
```

## Mailgun

### Description
Mailgun est un service d'API d'email conçu pour les développeurs. Il offre une haute délivrabilité et des fonctionnalités avancées pour les emails transactionnels.

### Configuration requise
- Clé API Mailgun
- Domaine vérifié sur Mailgun
- Adresse d'expéditeur
- Nom d'expéditeur

### Avantages
- Haute délivrabilité
- Suivi détaillé (ouvertures, clics)
- API simple et bien documentée
- Quota généreux en version gratuite (1000 emails/mois)

### Inconvénients
- Nécessite une vérification de domaine
- Payant au-delà du quota gratuit

### Exemple de configuration
```php
$config = [
    'api_key' => 'key-votre-cle-api-mailgun',
    'domain' => 'mg.votredomaine.com',
    'from_address' => 'noreply@votredomaine.com',
    'from_name' => 'Votre Application'
];
```

## Mailchimp

### Description
Mailchimp Transactional (anciennement Mandrill) est un service d'envoi d'emails transactionnels proposé par Mailchimp, particulièrement adapté pour les emails marketing et les newsletters.

### Configuration requise
- Clé API Mailchimp Transactional
- Adresse d'expéditeur vérifiée
- Nom d'expéditeur

### Avantages
- Excellente délivrabilité
- Outils avancés de suivi et d'analyse
- Modèles d'emails sophistiqués
- Intégration avec l'écosystème Mailchimp

### Inconvénients
- Service payant
- Configuration initiale plus complexe

### Exemple de configuration
```php
$config = [
    'api_key' => 'votre-cle-api-mailchimp',
    'from_address' => 'noreply@votredomaine.com',
    'from_name' => 'Votre Application'
];
```

## Rapidmail

### Description
Rapidmail est un service d'email marketing allemand qui respecte strictement le RGPD. Il est particulièrement adapté pour les entreprises européennes soucieuses de la conformité aux réglementations sur la protection des données.

### Configuration requise
- Clé API Rapidmail
- Adresse d'expéditeur vérifiée
- Nom d'expéditeur

### Avantages
- Conformité RGPD
- Serveurs basés en Europe
- Interface en français
- Bonne délivrabilité

### Inconvénients
- Moins connu que d'autres services
- Documentation moins étendue

### Exemple de configuration
```php
$config = [
    'api_key' => 'votre-cle-api-rapidmail',
    'from_address' => 'noreply@votredomaine.com',
    'from_name' => 'Votre Application'
];
```

## Comparaison des fournisseurs

| Fournisseur | Délivrabilité | Prix | Facilité de configuration | Fonctionnalités avancées | Conformité RGPD |
|-------------|---------------|------|---------------------------|--------------------------|----------------|
| SMTP        | Variable      | Gratuit | Modérée                   | Limitées                 | Dépend du serveur |
| PHPMail     | Variable      | Gratuit | Modérée                   | Moyennes                 | Dépend du serveur |
| Mailgun     | Élevée        | Freemium | Facile                    | Nombreuses               | Bonne |
| Mailchimp   | Très élevée   | Payant  | Modérée                   | Très nombreuses          | Bonne |
| Rapidmail   | Élevée        | Payant  | Facile                    | Nombreuses               | Excellente |

## Dépannage

### Problèmes courants

#### Les emails ne sont pas envoyés
- Vérifiez les informations d'identification
- Assurez-vous que le fournisseur est correctement configuré
- Vérifiez les quotas d'envoi
- Consultez les logs d'erreur

#### Emails reçus comme spam
- Vérifiez la configuration SPF, DKIM et DMARC de votre domaine
- Utilisez une adresse d'expéditeur vérifiée
- Évitez les mots déclencheurs de spam dans le sujet et le contenu
- Assurez-vous que votre domaine a une bonne réputation

#### Problèmes de configuration
- Pour SMTP/PHPMail : vérifiez les paramètres de port et de chiffrement
- Pour les API (Mailgun, Mailchimp, Rapidmail) : vérifiez la validité de la clé API
- Assurez-vous que les domaines et adresses d'expéditeur sont vérifiés

### Support

En cas de problème persistant, contactez le support technique du fournisseur d'email concerné :

- [Support Mailgun](https://help.mailgun.com/)
- [Support Mailchimp](https://mailchimp.com/contact/)
- [Support Rapidmail](https://www.rapidmail.fr/support)

Ou consultez la documentation officielle de chaque service pour des informations plus détaillées.
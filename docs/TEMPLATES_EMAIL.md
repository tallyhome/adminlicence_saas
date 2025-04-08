# Documentation des Templates d'Email

Ce document explique comment créer, modifier et prévisualiser les templates d'email dans l'application.

## Table des matières

1. [Introduction](#introduction)
2. [Création d'un template](#création-dun-template)
3. [Modification d'un template](#modification-dun-template)
4. [Prévisualisation des templates](#prévisualisation-des-templates)
5. [Variables dynamiques](#variables-dynamiques)
6. [Bonnes pratiques](#bonnes-pratiques)

## Introduction

Les templates d'email permettent de personnaliser les communications envoyées aux utilisateurs de l'application. Ils peuvent contenir des variables dynamiques qui seront remplacées par des valeurs réelles lors de l'envoi.

## Création d'un template

1. Accédez à la section **Gestion des templates d'email** dans le panneau d'administration
2. Remplissez les champs suivants :
   - **Nom du template** : un nom unique et descriptif
   - **Contenu du template** : le corps de l'email en HTML
   - **Variables** : ajoutez les variables que vous souhaitez utiliser dans le template
3. Utilisez le bouton **Prévisualiser** pour voir un aperçu du rendu final
4. Cliquez sur **Enregistrer le template** pour sauvegarder

## Modification d'un template

1. Dans la liste des templates existants, cliquez sur l'icône de modification (crayon) à côté du template souhaité
2. Modifiez les champs selon vos besoins
3. Utilisez le bouton **Prévisualiser** pour vérifier vos modifications
4. Cliquez sur **Mettre à jour** pour sauvegarder les changements

## Prévisualisation des templates

Il existe deux façons de prévisualiser un template d'email :

### Prévisualisation rapide

1. Lors de la création ou de la modification d'un template, cliquez sur le bouton **Prévisualiser** situé à côté de la section "Aperçu du template"
2. Une nouvelle fenêtre s'ouvrira avec une prévisualisation du template
3. Cette prévisualisation montre le contenu brut sans remplacer les variables

### Prévisualisation complète

1. Dans la liste des templates, cliquez sur l'icône d'œil à côté du template souhaité
2. Une page de prévisualisation s'ouvrira avec :
   - L'en-tête de l'email (expéditeur, destinataire, sujet)
   - Le corps de l'email avec les variables remplacées par des valeurs d'exemple
   - Un tableau des variables utilisées et leurs valeurs de test

## Variables dynamiques

Les variables permettent de personnaliser le contenu des emails. Elles sont entourées d'accolades, par exemple : `{nom}`, `{email}`, etc.

### Variables courantes

| Variable | Description | Exemple |
|----------|-------------|--------|
| {nom} | Nom du destinataire | Jean Dupont |
| {email} | Adresse email du destinataire | exemple@domaine.com |
| {date} | Date actuelle | 01/01/2023 |
| {licence} | Numéro de licence | XXXX-XXXX-XXXX-XXXX |
| {entreprise} | Nom de l'entreprise | Entreprise SAS |

### Ajout de variables

1. Dans le formulaire de création/modification, utilisez la section "Variables disponibles"
2. Entrez le nom de la variable (sans les accolades)
3. Cliquez sur le bouton **Ajouter**
4. La variable apparaîtra sous forme de badge bleu

### Utilisation des variables

Dans le contenu du template, insérez les variables en les entourant d'accolades :

```html
<p>Bonjour {nom},</p>
<p>Votre licence {licence} a été activée pour {entreprise}.</p>
<p>Date d'activation : {date}</p>
```

## Bonnes pratiques

### Structure des emails

- Utilisez un en-tête clair avec le logo de l'entreprise
- Commencez par une salutation personnalisée
- Gardez le contenu concis et structuré
- Terminez par une signature et des coordonnées de contact

### Conception responsive

- Utilisez des tableaux pour la structure (meilleure compatibilité avec les clients email)
- Limitez la largeur à 600-800px maximum
- Utilisez des polices sans-serif (Arial, Helvetica, etc.)
- Testez sur différents clients email

### Contenu

- Évitez les images trop grandes ou trop nombreuses
- N'utilisez pas de JavaScript (non supporté par la plupart des clients email)
- Limitez l'utilisation des CSS complexes
- Incluez toujours une version texte pour les clients qui bloquent le HTML

### Exemple de structure HTML recommandée

```html
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center">
      <table width="600" border="0" cellspacing="0" cellpadding="0">
        <!-- En-tête -->
        <tr>
          <td style="padding: 20px 0;">
            <img src="https://votredomaine.com/logo.png" alt="Logo" width="150">
          </td>
        </tr>
        
        <!-- Corps -->
        <tr>
          <td style="background-color: #ffffff; padding: 20px; border-radius: 4px;">
            <p>Bonjour {nom},</p>
            <p>Votre licence {licence} a été activée pour {entreprise}.</p>
            <p>Date d'activation : {date}</p>
          </td>
        </tr>
        
        <!-- Pied de page -->
        <tr>
          <td style="padding: 20px; font-size: 12px; color: #666;">
            <p>© 2023 Votre Entreprise. Tous droits réservés.</p>
            <p>Pour nous contacter : support@votredomaine.com</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
```
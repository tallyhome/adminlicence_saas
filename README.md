# AdminLicence - Système de gestion de licences

AdminLicence est un système complet de gestion de licences pour vos applications. Il permet de créer, gérer et valider des licences pour vos projets logiciels via une interface d'administration conviviale et une API sécurisée.

## Fonctionnalités

- **Gestion de projets** : Créez et gérez différents projets nécessitant des licences
- **Génération de clés de série** : Générez des clés uniques pour chaque projet
- **Validation de licences** : API sécurisée pour valider les licences dans vos applications
- **Restrictions par domaine et IP** : Limitez l'utilisation des licences à des domaines ou adresses IP spécifiques
- **Dates d'expiration** : Définissez des dates d'expiration pour vos licences
- **Codes dynamiques sécurisés** : Système de codes changeant périodiquement pour une sécurité renforcée

<p align="center">

## Installation

### Prérequis

- PHP 8.1 ou supérieur
- Composer
- Base de données MySQL, PostgreSQL ou SQLite

### Étapes d'installation

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/votre-compte/adminlicence.git
   cd adminlicence
   ```

2. Installez les dépendances :
   ```bash
   composer install
   ```

3. Copiez le fichier d'environnement :
   ```bash
   cp .env.example .env
   ```

4. Configurez votre base de données dans le fichier `.env`

5. Générez la clé d'application :
   ```bash
   php artisan key:generate
   ```

6. Exécutez les migrations :
   ```bash
   php artisan migrate
   ```

7. Lancez le serveur de développement :
   ```bash
   php artisan serve
   ```

## Utilisation

### Interface d'administration

1. Accédez à l'interface d'administration à l'adresse `http://localhost:8000/admin`
2. Connectez-vous avec vos identifiants
3. Créez un nouveau projet via le menu "Projets"
4. Générez des clés de série pour ce projet

### Intégration de l'API dans vos applications

L'API de validation des licences est accessible via les endpoints suivants :

- `POST /api/v1/check-serial` : Vérifier la validité d'une clé de série
- `GET /api/v1/get-secure-code` : Récupérer un code dynamique sécurisé

Consultez la documentation complète de l'API dans la section "Exemple d'intégration client" de l'interface d'administration.

## Sécurité

Le système utilise plusieurs niveaux de sécurité :

1. **Validation de domaine et IP** : Les licences peuvent être liées à des domaines et adresses IP spécifiques
2. **Tokens temporaires** : Chaque vérification génère un token temporaire valide 24h
3. **Codes dynamiques** : Les codes sécurisés changent toutes les heures
4. **Révocation de licences** : Possibilité de révoquer instantanément une licence

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

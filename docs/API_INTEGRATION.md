# Guide d'intégration de l'API AdminLicence

Ce document fournit des instructions détaillées pour intégrer l'API AdminLicence dans vos applications, que ce soit avec PHP standard, Laravel ou Flutter.

## Table des matières

- [Authentification avec les clés API](#authentification-avec-les-clés-api)
- [Intégration des clés de licence](#intégration-des-clés-de-licence)
- [Exemples d'intégration](#exemples-dintégration)
  - [PHP Standard](#php-standard)
  - [Laravel](#laravel)
  - [Flutter](#flutter)
- [Référence de l'API](#référence-de-lapi)

## Authentification avec les clés API

Les clés API permettent d'accéder de manière sécurisée à l'API AdminLicence pour effectuer diverses opérations comme la création, la modification ou la suppression de licences.

### Obtention des clés API

1. Connectez-vous à l'interface d'administration d'AdminLicence
2. Accédez à la section "API Keys" ou "Clés API"
3. Cliquez sur "Créer une nouvelle clé API"
4. Sélectionnez le projet auquel associer cette clé
5. Définissez les permissions appropriées
6. Cliquez sur "Générer"
7. Notez la clé API (`api_key`) et le secret (`api_secret`) - **le secret ne sera affiché qu'une seule fois**

### Utilisation des clés API

Pour authentifier vos requêtes API, vous devez inclure vos identifiants API dans l'en-tête de chaque requête :

```
Authorization: Bearer {api_key}:{api_secret}
```

Exemple d'en-tête HTTP :
```
Authorization: Bearer sk_iP8sb2tbaEwOwdRT8qwBfkGeS4B3wTRk:sk_MvKAHethKtcUAc8JacgY6aiX2sZMHaq6
```

## Intégration des clés de licence

Les clés de licence (ou clés de série) sont utilisées pour activer et valider vos applications clientes. Contrairement aux clés API, elles sont destinées à être distribuées à vos utilisateurs finaux.

### Endpoints de vérification de licence

AdminLicence propose plusieurs endpoints pour vérifier les licences :

1. **API standard** : `/api/check-serial.php`
2. **API v1** : `/api/v1/check-serial.php`
3. **API simple** : `/api/simple-check.php`
4. **API ultra-simple** : `/api/ultra-simple.php`

Chaque endpoint accepte les mêmes paramètres mais peut avoir des caractéristiques différentes en termes de performance et de dépendances.

### Format de requête

Toutes les requêtes de vérification de licence doivent être envoyées en POST avec un corps JSON contenant :

```json
{
  "serial_key": "XXXX-XXXX-XXXX-XXXX",
  "domain": "example.com",
  "ip_address": "192.168.1.1"
}
```

- `serial_key` : La clé de licence à vérifier (obligatoire)
- `domain` : Le domaine sur lequel la licence est utilisée (optionnel)
- `ip_address` : L'adresse IP de l'utilisateur (optionnel)

### Format de réponse

La réponse sera au format JSON avec la structure suivante :

```json
{
  "status": "success",
  "message": "Clé de série valide",
  "data": {
    "token": "2c23f1f8446f32b89bd58cb51da1897d",
    "project": "Nom du projet",
    "expires_at": "2025-12-31"
  }
}
```

En cas d'erreur :

```json
{
  "status": "error",
  "message": "Clé de série invalide ou inactive",
  "data": null
}
```

## Exemples d'intégration

### PHP Standard

#### Vérification de licence avec PHP standard

```php
<?php
/**
 * Fonction pour vérifier une licence
 * 
 * @param string $cleSeriale Clé de licence à vérifier
 * @param string $domaine Domaine du site (optionnel)
 * @param string $adresseIP Adresse IP du serveur (optionnel)
 * @return array Résultat de la vérification
 */
function verifierLicence($cleSeriale, $domaine = null, $adresseIP = null) {
    // URL de l'API de vérification
    $url = "https://licence.myvcard.fr/api/check-serial.php";
    
    // Données à envoyer
    $donnees = [
        'serial_key' => $cleSeriale,
        'domain' => $domaine ?: $_SERVER['SERVER_NAME'],
        'ip_address' => $adresseIP ?: $_SERVER['REMOTE_ADDR']
    ];
    
    // Initialiser cURL
    $ch = curl_init($url);
    
    // Configurer cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($donnees));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout de 10 secondes
    
    // Exécuter la requête
    $reponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Décoder la réponse JSON
    $resultat = json_decode($reponse, true);
    
    // Préparer le résultat
    return [
        'valide' => ($httpCode == 200 && isset($resultat['status']) && $resultat['status'] === 'success'),
        'message' => $resultat['message'] ?? 'Erreur inconnue',
        'donnees' => $resultat['data'] ?? null
    ];
}

// Exemple d'utilisation
$resultat = verifierLicence('XXXX-XXXX-XXXX-XXXX');

if ($resultat['valide']) {
    // La licence est valide, activer les fonctionnalités
    echo "Licence valide! Vous pouvez utiliser l'application.";
    
    // Vous pouvez accéder aux données supplémentaires
    $token = $resultat['donnees']['token'] ?? '';
    $projet = $resultat['donnees']['project'] ?? '';
    $expiration = $resultat['donnees']['expires_at'] ?? null;
} else {
    // La licence est invalide, limiter les fonctionnalités
    echo "Erreur de licence: " . $resultat['message'];
}
```

#### Utilisation des clés API avec PHP standard

```php
<?php
/**
 * Fonction pour créer une nouvelle licence avec l'API
 * 
 * @param string $apiKey Clé API
 * @param string $apiSecret Secret API
 * @param string $projectId ID du projet
 * @param string $expiresAt Date d'expiration (format YYYY-MM-DD)
 * @return array Résultat de la création
 */
function creerLicence($apiKey, $apiSecret, $projectId, $expiresAt = null) {
    // URL de l'API
    $url = "https://licence.myvcard.fr/api/v1/licences";
    
    // Données à envoyer
    $donnees = [
        'project_id' => $projectId,
        'expires_at' => $expiresAt
    ];
    
    // Initialiser cURL
    $ch = curl_init($url);
    
    // Configurer cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($donnees));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey . ':' . $apiSecret
    ]);
    
    // Exécuter la requête
    $reponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Décoder la réponse JSON
    return json_decode($reponse, true);
}

// Exemple d'utilisation
$apiKey = 'sk_iP8sb2tbaEwOwdRT8qwBfkGeS4B3wTRk';
$apiSecret = 'sk_MvKAHethKtcUAc8JacgY6aiX2sZMHaq6';
$projectId = '1'; // ID du projet
$expiresAt = '2025-12-31'; // Date d'expiration

$resultat = creerLicence($apiKey, $apiSecret, $projectId, $expiresAt);

if (isset($resultat['status']) && $resultat['status'] === 'success') {
    echo "Licence créée avec succès: " . $resultat['data']['serial_key'];
} else {
    echo "Erreur lors de la création de la licence: " . ($resultat['message'] ?? 'Erreur inconnue');
}
```

### Laravel

#### Vérification de licence avec Laravel

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LicenceService
{
    protected $apiUrl;
    
    public function __construct()
    {
        $this->apiUrl = config('services.licence.url', 'https://licence.myvcard.fr/api/check-serial.php');
    }
    
    /**
     * Vérifier la validité d'une licence
     *
     * @param string $serialKey Clé de licence
     * @param string|null $domain Domaine
     * @param string|null $ipAddress Adresse IP
     * @return array
     */
    public function verifyLicence(string $serialKey, ?string $domain = null, ?string $ipAddress = null): array
    {
        try {
            $response = Http::post($this->apiUrl, [
                'serial_key' => $serialKey,
                'domain' => $domain ?? request()->getHost(),
                'ip_address' => $ipAddress ?? request()->ip()
            ]);
            
            $data = $response->json();
            
            return [
                'valid' => $response->successful() && isset($data['status']) && $data['status'] === 'success',
                'message' => $data['message'] ?? 'Erreur inconnue',
                'data' => $data['data'] ?? null
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Erreur de connexion à l\'API de licence: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
```

Configuration dans `config/services.php` :

```php
'licence' => [
    'url' => env('LICENCE_API_URL', 'https://licence.myvcard.fr/api/check-serial.php'),
],
```

Utilisation dans un contrôleur :

```php
<?php

namespace App\Http\Controllers;

use App\Services\LicenceService;
use Illuminate\Http\Request;

class LicenceController extends Controller
{
    protected $licenceService;
    
    public function __construct(LicenceService $licenceService)
    {
        $this->licenceService = $licenceService;
    }
    
    public function verify(Request $request)
    {
        $request->validate([
            'serial_key' => 'required|string'
        ]);
        
        $result = $this->licenceService->verifyLicence(
            $request->input('serial_key'),
            $request->input('domain'),
            $request->input('ip_address')
        );
        
        if ($result['valid']) {
            return response()->json([
                'success' => true,
                'message' => 'Licence valide',
                'data' => $result['data']
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }
}
```

#### Utilisation des clés API avec Laravel

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AdminLicenceApiService
{
    protected $baseUrl;
    protected $apiKey;
    protected $apiSecret;
    
    public function __construct()
    {
        $this->baseUrl = config('services.adminlicence.url', 'https://licence.myvcard.fr/api/v1');
        $this->apiKey = config('services.adminlicence.key');
        $this->apiSecret = config('services.adminlicence.secret');
    }
    
    /**
     * Créer une nouvelle licence
     *
     * @param string $projectId ID du projet
     * @param string|null $expiresAt Date d'expiration (format YYYY-MM-DD)
     * @return array
     */
    public function createLicence(string $projectId, ?string $expiresAt = null): array
    {
        return $this->sendRequest('POST', '/licences', [
            'project_id' => $projectId,
            'expires_at' => $expiresAt
        ]);
    }
    
    /**
     * Obtenir la liste des licences
     *
     * @param int $page Numéro de page
     * @param int $perPage Nombre d'éléments par page
     * @return array
     */
    public function getLicences(int $page = 1, int $perPage = 15): array
    {
        return $this->sendRequest('GET', '/licences', [
            'page' => $page,
            'per_page' => $perPage
        ]);
    }
    
    /**
     * Envoyer une requête à l'API
     *
     * @param string $method Méthode HTTP
     * @param string $endpoint Point d'entrée de l'API
     * @param array $data Données à envoyer
     * @return array
     */
    protected function sendRequest(string $method, string $endpoint, array $data = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey . ':' . $this->apiSecret
            ])->$method($this->baseUrl . $endpoint, $method === 'GET' ? [] : $data);
            
            return $response->json();
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erreur de connexion à l\'API: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
```

Configuration dans `config/services.php` :

```php
'adminlicence' => [
    'url' => env('ADMINLICENCE_API_URL', 'https://licence.myvcard.fr/api/v1'),
    'key' => env('ADMINLICENCE_API_KEY'),
    'secret' => env('ADMINLICENCE_API_SECRET'),
],
```

### Flutter

#### Vérification de licence avec Flutter

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class LicenceService {
  final String apiUrl;
  
  LicenceService({this.apiUrl = 'https://licence.myvcard.fr/api/check-serial.php'});
  
  /// Vérifie la validité d'une licence
  /// 
  /// [serialKey] : Clé de licence à vérifier
  /// [domain] : Domaine (optionnel)
  /// [ipAddress] : Adresse IP (optionnel)
  Future<Map<String, dynamic>> verifyLicence(String serialKey, {String? domain, String? ipAddress}) async {
    try {
      final response = await http.post(
        Uri.parse(apiUrl),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'serial_key': serialKey,
          'domain': domain,
          'ip_address': ipAddress,
        }),
      );
      
      final Map<String, dynamic> data = jsonDecode(response.body);
      
      return {
        'valid': response.statusCode == 200 && data['status'] == 'success',
        'message': data['message'] ?? 'Erreur inconnue',
        'data': data['data'],
      };
    } catch (e) {
      return {
        'valid': false,
        'message': 'Erreur de connexion à l\'API de licence: $e',
        'data': null,
      };
    }
  }
}

// Exemple d'utilisation dans un widget
import 'package:flutter/material.dart';

class LicenceVerificationScreen extends StatefulWidget {
  @override
  _LicenceVerificationScreenState createState() => _LicenceVerificationScreenState();
}

class _LicenceVerificationScreenState extends State<LicenceVerificationScreen> {
  final _formKey = GlobalKey<FormState>();
  final _licenceService = LicenceService();
  final _serialKeyController = TextEditingController();
  
  bool _isLoading = false;
  String _message = '';
  bool _isValid = false;
  
  Future<void> _verifyLicence() async {
    if (!_formKey.currentState!.validate()) return;
    
    setState(() {
      _isLoading = true;
      _message = '';
    });
    
    try {
      final result = await _licenceService.verifyLicence(_serialKeyController.text);
      
      setState(() {
        _isLoading = false;
        _isValid = result['valid'];
        _message = result['message'];
      });
      
      if (_isValid) {
        // Sauvegarder la licence et rediriger vers l'écran principal
        Navigator.of(context).pushReplacementNamed('/home');
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
        _isValid = false;
        _message = 'Erreur: $e';
      });
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Vérification de licence')),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              TextFormField(
                controller: _serialKeyController,
                decoration: InputDecoration(
                  labelText: 'Clé de licence',
                  hintText: 'Entrez votre clé de licence',
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Veuillez entrer une clé de licence';
                  }
                  return null;
                },
              ),
              SizedBox(height: 24),
              if (_isLoading)
                CircularProgressIndicator()
              else
                ElevatedButton(
                  onPressed: _verifyLicence,
                  child: Text('Vérifier'),
                ),
              SizedBox(height: 16),
              if (_message.isNotEmpty)
                Container(
                  padding: EdgeInsets.all(8),
                  color: _isValid ? Colors.green.shade100 : Colors.red.shade100,
                  child: Text(
                    _message,
                    style: TextStyle(
                      color: _isValid ? Colors.green.shade800 : Colors.red.shade800,
                    ),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}
```

#### Utilisation des clés API avec Flutter

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class AdminLicenceApiService {
  final String baseUrl;
  final String apiKey;
  final String apiSecret;
  
  AdminLicenceApiService({
    this.baseUrl = 'https://licence.myvcard.fr/api/v1',
    required this.apiKey,
    required this.apiSecret,
  });
  
  /// Crée une nouvelle licence
  /// 
  /// [projectId] : ID du projet
  /// [expiresAt] : Date d'expiration (format YYYY-MM-DD)
  Future<Map<String, dynamic>> createLicence(String projectId, {String? expiresAt}) async {
    return await _sendRequest(
      'POST',
      '/licences',
      {
        'project_id': projectId,
        'expires_at': expiresAt,
      },
    );
  }
  
  /// Récupère la liste des licences
  /// 
  /// [page] : Numéro de page
  /// [perPage] : Nombre d'éléments par page
  Future<Map<String, dynamic>> getLicences({int page = 1, int perPage = 15}) async {
    return await _sendRequest(
      'GET',
      '/licences',
      {
        'page': page.toString(),
        'per_page': perPage.toString(),
      },
    );
  }
  
  /// Envoie une requête à l'API
  /// 
  /// [method] : Méthode HTTP
  /// [endpoint] : Point d'entrée de l'API
  /// [data] : Données à envoyer
  Future<Map<String, dynamic>> _sendRequest(String method, String endpoint, Map<String, dynamic> data) async {
    try {
      final Uri uri = method == 'GET'
          ? Uri.parse('$baseUrl$endpoint').replace(queryParameters: data)
          : Uri.parse('$baseUrl$endpoint');
      
      final http.Response response;
      
      switch (method) {
        case 'GET':
          response = await http.get(
            uri,
            headers: _getHeaders(),
          );
          break;
        case 'POST':
          response = await http.post(
            uri,
            headers: _getHeaders(),
            body: jsonEncode(data),
          );
          break;
        case 'PUT':
          response = await http.put(
            uri,
            headers: _getHeaders(),
            body: jsonEncode(data),
          );
          break;
        case 'DELETE':
          response = await http.delete(
            uri,
            headers: _getHeaders(),
          );
          break;
        default:
          throw Exception('Méthode HTTP non supportée: $method');
      }
      
      return jsonDecode(response.body);
    } catch (e) {
      return {
        'status': 'error',
        'message': 'Erreur de connexion à l\'API: $e',
        'data': null,
      };
    }
  }
  
  /// Retourne les en-têtes HTTP avec l'authentification
  Map<String, String> _getHeaders() {
    return {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $apiKey:$apiSecret',
    };
  }
}

// Exemple d'utilisation dans un widget
import 'package:flutter/material.dart';

class CreateLicenceScreen extends StatefulWidget {
  @override
  _CreateLicenceScreenState createState() => _CreateLicenceScreenState();
}

class _CreateLicenceScreenState extends State<CreateLicenceScreen> {
  final _formKey = GlobalKey<FormState>();
  final _apiService = AdminLicenceApiService(
    apiKey: 'sk_iP8sb2tbaEwOwdRT8qwBfkGeS4B3wTRk',
    apiSecret: 'sk_MvKAHethKtcUAc8JacgY6aiX2sZMHaq6',
  );
  
  final _projectIdController = TextEditingController();
  final _expiresAtController = TextEditingController();
  
  bool _isLoading = false;
  String _message = '';
  String _createdLicence = '';
  
  Future<void> _createLicence() async {
    if (!_formKey.currentState!.validate()) return;
    
    setState(() {
      _isLoading = true;
      _message = '';
      _createdLicence = '';
    });
    
    try {
      final result = await _apiService.createLicence(
        _projectIdController.text,
        expiresAt: _expiresAtController.text.isNotEmpty ? _expiresAtController.text : null,
      );
      
      setState(() {
        _isLoading = false;
        
        if (result['status'] == 'success') {
          _message = 'Licence créée avec succès!';
          _createdLicence = result['data']['serial_key'];
        } else {
          _message = 'Erreur: ${result['message']}';
        }
      });
    } catch (e) {
      setState(() {
        _isLoading = false;
        _message = 'Erreur: $e';
      });
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Créer une licence')),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              TextFormField(
                controller: _projectIdController,
                decoration: InputDecoration(
                  labelText: 'ID du projet',
                  hintText: 'Entrez l\'ID du projet',
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Veuillez entrer l\'ID du projet';
                  }
                  return null;
                },
              ),
              SizedBox(height: 16),
              TextFormField(
                controller: _expiresAtController,
                decoration: InputDecoration(
                  labelText: 'Date d\'expiration (YYYY-MM-DD)',
                  hintText: 'Optionnel',
                ),
              ),
              SizedBox(height: 24),
              if (_isLoading)
                Center(child: CircularProgressIndicator())
              else
                Center(
                  child: ElevatedButton(
                    onPressed: _createLicence,
                    child: Text('Créer une licence'),
                  ),
                ),
              SizedBox(height: 16),
              if (_message.isNotEmpty)
                Container(
                  padding: EdgeInsets.all(8),
                  color: _createdLicence.isNotEmpty ? Colors.green.shade100 : Colors.red.shade100,
                  child: Text(
                    _message,
                    style: TextStyle(
                      color: _createdLicence.isNotEmpty ? Colors.green.shade800 : Colors.red.shade800,
                    ),
                  ),
                ),
              if (_createdLicence.isNotEmpty) ...[
                SizedBox(height: 16),
                Text('Clé de licence:', style: TextStyle(fontWeight: FontWeight.bold)),
                SizedBox(height: 8),
                SelectableText(
                  _createdLicence,
                  style: TextStyle(fontSize: 18),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
```

## Référence de l'API

### Endpoints pour les clés de licence

| Endpoint | Méthode | Description |
|----------|---------|-------------|
| `/api/check-serial.php` | POST | Vérifie une clé de licence (API standard) |
| `/api/v1/check-serial.php` | POST | Vérifie une clé de licence (API v1) |
| `/api/simple-check.php` | POST | Vérifie une clé de licence (API simple) |
| `/api/ultra-simple.php` | POST | Vérifie une clé de licence (API ultra-simple) |

### Endpoints pour les clés API

| Endpoint | Méthode | Description | Permissions requises |
|----------|---------|-------------|---------------------|
| `/api/v1/licences` | GET | Liste toutes les licences | Lecture des licences |
| `/api/v1/licences` | POST | Crée une nouvelle licence | Écriture des licences |
| `/api/v1/licences/{id}` | GET | Récupère les détails d'une licence | Lecture des licences |
| `/api/v1/licences/{id}` | PUT | Met à jour une licence | Écriture des licences |
| `/api/v1/licences/{id}` | DELETE | Supprime une licence | Suppression des licences |
| `/api/v1/projects` | GET | Liste tous les projets | Lecture des projets |
| `/api/v1/projects` | POST | Crée un nouveau projet | Écriture des projets |
| `/api/v1/projects/{id}` | GET | Récupère les détails d'un projet | Lecture des projets |
| `/api/v1/projects/{id}` | PUT | Met à jour un projet | Écriture des projets |
| `/api/v1/projects/{id}` | DELETE | Supprime un projet | Suppression des projets |
| `/api/v1/stats` | GET | Récupère les statistiques | Lecture des statistiques |

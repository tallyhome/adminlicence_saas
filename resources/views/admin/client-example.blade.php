@extends('layouts.admin')

@section('title', 'Exemple de client API')

@section('header', 'Exemple d\'intégration client')

@section('content')
<div class="mb-4">
    <h2 class="text-xl font-semibold text-gray-800">Guide d'intégration de l'API de licence</h2>
    <p class="mt-2 text-gray-600">Ce guide explique comment intégrer le système de licence dans vos applications.</p>
</div>

<!-- Documentation de l'API -->
<div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Documentation de l'API</h3>
    </div>
    <div class="border-t border-gray-200 px-4 py-5">
        <h4 class="text-md font-medium text-gray-900 mb-2">1. Vérification d'une clé de série</h4>
        <div class="bg-gray-50 p-4 rounded-md mb-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Endpoint:</p>
            <pre class="bg-gray-800 text-white p-2 rounded-md overflow-x-auto"><code>POST /api/v1/check-serial</code></pre>
            
            <p class="text-sm font-medium text-gray-700 mt-3 mb-2">Paramètres:</p>
            <pre class="bg-gray-800 text-white p-2 rounded-md overflow-x-auto"><code>{
  "serial_key": "XXXX-XXXX-XXXX-XXXX",
  "domain": "exemple.com",     // Optionnel
  "ip_address": "192.168.1.1"  // Optionnel
}</code></pre>
            
            <p class="text-sm font-medium text-gray-700 mt-3 mb-2">Réponse en cas de succès:</p>
            <pre class="bg-gray-800 text-white p-2 rounded-md overflow-x-auto"><code>{
  "status": "success",
  "message": "Clé de série valide",
  "data": {
    "token": "token_temporaire_pour_authentification",
    "project": "Nom du projet",
    "expires_at": "2023-12-31T23:59:59.000000Z"
  }
}</code></pre>
        </div>
        
        <h4 class="text-md font-medium text-gray-900 mb-2">2. Récupération du code sécurisé</h4>
        <div class="bg-gray-50 p-4 rounded-md mb-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Endpoint:</p>
            <pre class="bg-gray-800 text-white p-2 rounded-md overflow-x-auto"><code>GET /api/v1/get-secure-code</code></pre>
            
            <p class="text-sm font-medium text-gray-700 mt-3 mb-2">Paramètres:</p>
            <pre class="bg-gray-800 text-white p-2 rounded-md overflow-x-auto"><code>{
  "token": "token_obtenu_lors_de_la_verification",
  "serial_key": "XXXX-XXXX-XXXX-XXXX"
}</code></pre>
            
            <p class="text-sm font-medium text-gray-700 mt-3 mb-2">Réponse en cas de succès:</p>
            <pre class="bg-gray-800 text-white p-2 rounded-md overflow-x-auto"><code>{
  "status": "success",
  "data": {
    "secure_code": "code_securise_dynamique",
    "valid_until": "2023-04-03T15:00:00.000000Z"
  }
}</code></pre>
        </div>
    </div>
</div>

<!-- Exemple d'intégration -->
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Exemple d'intégration en PHP</h3>
    </div>
    <div class="border-t border-gray-200 px-4 py-5">
        <pre class="bg-gray-800 text-white p-4 rounded-md overflow-x-auto"><code>&lt;?php
/**
 * Exemple d'intégration du système de licence dans une application PHP
 */

class LicenceValidator {
    private $apiUrl;
    private $serialKey;
    private $token;
    private $secureCode;
    private $validUntil;
    
    public function __construct(string $apiUrl, string $serialKey) {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->serialKey = $serialKey;
    }
    
    /**
     * Vérifie la validité de la clé de série
     */
    public function verifyLicence(string $domain = null, string $ipAddress = null): bool {
        $data = [
            'serial_key' => $this->serialKey
        ];
        
        if ($domain) {
            $data['domain'] = $domain;
        }
        
        if ($ipAddress) {
            $data['ip_address'] = $ipAddress;
        }
        
        $response = $this->makeApiRequest('/api/v1/check-serial', 'POST', $data);
        
        if ($response && $response['status'] === 'success') {
            $this->token = $response['data']['token'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Récupère le code sécurisé dynamique
     */
    public function getSecureCode(): ?string {
        if (!$this->token) {
            return null;
        }
        
        // Vérifier si le code actuel est encore valide
        if ($this->secureCode && $this->validUntil && strtotime($this->validUntil) > time()) {
            return $this->secureCode;
        }
        
        $data = [
            'token' => $this->token,
            'serial_key' => $this->serialKey
        ];
        
        $response = $this->makeApiRequest('/api/v1/get-secure-code', 'GET', $data);
        
        if ($response && $response['status'] === 'success') {
            $this->secureCode = $response['data']['secure_code'];
            $this->validUntil = $response['data']['valid_until'];
            return $this->secureCode;
        }
        
        return null;
    }
    
    /**
     * Effectue une requête vers l'API
     */
    private function makeApiRequest(string $endpoint, string $method, array $data) {
        $url = $this->apiUrl . $endpoint;
        
        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => $method,
                'content' => json_encode($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        
        if ($result === false) {
            return null;
        }
        
        return json_decode($result, true);
    }
}

// Exemple d'utilisation
$validator = new LicenceValidator('https://licence.exemple.com', 'ABCD-EFGH-IJKL-MNOP');

// Vérifier la licence
if ($validator->verifyLicence('monsite.com')) {
    echo "Licence valide!\n";
    
    // Récupérer le code sécurisé
    $secureCode = $validator->getSecureCode();
    if ($secureCode) {
        echo "Code sécurisé: " . $secureCode . "\n";
        
        // Utiliser ce code pour débloquer des fonctionnalités
        // ou vérifier périodiquement la validité de la licence
    }
} else {
    echo "Licence invalide ou expirée.\n";
}
</code></pre>
    </div>
</div>

<!-- Exemple d'intégration JavaScript -->
<div class="bg-white shadow overflow-hidden sm:rounded-lg mt-6">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Exemple d'intégration en JavaScript</h3>
    </div>
    <div class="border-t border-gray-200 px-4 py-5">
        <pre class="bg-gray-800 text-white p-4 rounded-md overflow-x-auto"><code>/**
 * Exemple d'intégration du système de licence dans une application JavaScript
 */

class LicenceValidator {
    constructor(apiUrl, serialKey) {
        this.apiUrl = apiUrl.endsWith('/') ? apiUrl.slice(0, -1) : apiUrl;
        this.serialKey = serialKey;
        this.token = null;
        this.secureCode = null;
        this.validUntil = null;
    }
    
    /**
     * Vérifie la validité de la clé de série
     */
    async verifyLicence(domain = null, ipAddress = null) {
        const data = {
            serial_key: this.serialKey
        };
        
        if (domain) {
            data.domain = domain;
        }
        
        if (ipAddress) {
            data.ip_address = ipAddress;
        }
        
        try {
            const response = await this.makeApiRequest('/api/v1/check-serial', 'POST', data);
            
            if (response && response.status === 'success') {
                this.token = response.data.token;
                return true;
            }
            
            return false;
        } catch (error) {
            console.error('Erreur lors de la vérification de la licence:', error);
            return false;
        }
    }
    
    /**
     * Récupère le code sécurisé dynamique
     */
    async getSecureCode() {
        if (!this.token) {
            return null;
        }
        
        // Vérifier si le code actuel est encore valide
        if (this.secureCode && this.validUntil && new Date(this.validUntil) > new Date()) {
            return this.secureCode;
        }
        
        const data = {
            token: this.token,
            serial_key: this.serialKey
        };
        
        try {
            const response = await this.makeApiRequest('/api/v1/get-secure-code', 'GET', data);
            
            if (response && response.status === 'success') {
                this.secureCode = response.data.secure_code;
                this.validUntil = response.data.valid_until;
                return this.secureCode;
            }
            
            return null;
        } catch (error) {
            console.error('Erreur lors de la récupération du code sécurisé:', error);
            return null;
        }
    }
    
    /**
     * Effectue une requête vers l'API
     */
    async makeApiRequest(endpoint, method, data) {
        const url = this.apiUrl + endpoint;
        
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        };
        
        const response = await fetch(url, options);
        return await response.json();
    }
}

// Exemple d'utilisation
async function checkLicence() {
    const validator = new LicenceValidator('https://licence.exemple.com', 'ABCD-EFGH-IJKL-MNOP');
    
    // Vérifier la licence
    const isValid = await validator.verifyLicence('monsite.com');
    
    if (isValid) {
        console.log('Licence valide!');
        
        // Récupérer le code sécurisé
        const secureCode = await validator.getSecureCode();
        if (secureCode) {
            console.log('Code sécurisé:', secureCode);
            
            // Utiliser ce code pour débloquer des fonctionnalités
            // ou vérifier périodiquement la validité de la licence
        }
    } else {
        console.log('Licence invalide ou expirée.');
    }
}

checkLicence();
</code></pre>
    </div>
</div>
@endsection
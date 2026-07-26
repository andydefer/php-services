# IpGeolocationService - Référence Technique

## Description

Service de géolocalisation IP utilisant l'API publique ip-api.com pour obtenir des informations géographiques et réseau à partir d'une adresse IP.

## Hiérarchie / Implémentations

```
IpGeolocationService
```

**Aucune interface implémentée** - Service autonome dédié à la géolocalisation IP.

## Rôle principal

`IpGeolocationService` agit comme un client HTTP spécialisé pour l'API ip-api.com. Il assure :

- La **validation** des adresses IP (IPv4 et IPv6)
- L'**envoi** des requêtes HTTP vers l'API ip-api.com
- La **gestion** des erreurs et des timeouts
- La **transformation** des réponses JSON en objets PHP typés
- La **normalisation** des données de géolocalisation

## DETAILS

[Voir la classe IpGeolocationService](https://github.com/andydefer/php-services/blob/main/src/Services/IpGeolocationService.php)

## API / Méthodes publiques

### `__construct(?ClientService $client = null)`

Crée une nouvelle instance du service de géolocalisation.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$client` | `ClientService|null` | Client HTTP personnalisé (optionnel) |

**Exemple :**
```php
// Utilisation du client par défaut
$service = new IpGeolocationService();

// Avec un client personnalisé
$client = new ClientService();
$service = new IpGeolocationService($client);
```

---

### `locate(string $ip): IpGeoSuccessStruct`

Récupère les données de géolocalisation pour une adresse IP donnée.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$ip` | `string` | Adresse IP à localiser (IPv4 ou IPv6) |

**Retourne :** `IpGeoSuccessStruct` - Données de géolocalisation

**Exceptions :**
- `InvalidArgumentException` - IP vide ou invalide
- `RuntimeException` - Échec de la requête API

**Exemple :**
```php
$service = new IpGeolocationService();
$result = $service->locate('8.8.8.8');

echo $result->country;    // 'United States'
echo $result->city;       // 'Mountain View'
echo $result->isp;        // 'Google LLC'
```

---

### `locateRaw(string $ip): IpGeoResponse`

Récupère les données de géolocalisation et retourne la réponse brute sans lever d'exception.

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$ip` | `string` | Adresse IP à localiser (IPv4 ou IPv6) |

**Retourne :** `IpGeoResponse` - Réponse brute contenant succès ou échec

**Exceptions :**
- `InvalidArgumentException` - IP vide ou invalide

**Exemple :**
```php
$service = new IpGeolocationService();
$response = $service->locateRaw('8.8.8.8');

if ($response->isSuccess()) {
    $data = $response->getSuccessData();
    echo $data->city;
} else {
    echo $response->getErrorMessage();
}
```

---

## Cas d'utilisation

### Cas 1 : Géolocaliser un visiteur sur un site web

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\IpGeolocationService;

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$service = new IpGeolocationService();

try {
    $location = $service->locate($ip);
    
    echo "Bienvenue depuis {$location->city}, {$location->country}!";
    echo "Votre FAI est : {$location->isp}";
    
} catch (\Exception $e) {
    // Log de l'erreur mais continuer l'exécution
    error_log('Geolocation failed: ' . $e->getMessage());
}
```

### Cas 2 : Enregistrer la localisation d'un utilisateur

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\IpGeolocationService;

class UserRegistrationService
{
    private IpGeolocationService $geoService;
    
    public function __construct()
    {
        $this->geoService = new IpGeolocationService();
    }
    
    public function registerUser(string $email, string $ip): void
    {
        try {
            $location = $this->geoService->locate($ip);
            
            // Enregistrer l'utilisateur avec ses infos de localisation
            $userData = [
                'email' => $email,
                'registration_ip' => $ip,
                'registration_country' => $location->country,
                'registration_city' => $location->city,
                'registration_isp' => $location->isp,
                'latitude' => $location->lat,
                'longitude' => $location->lon,
            ];
            
            // Sauvegarder en base de données...
            
        } catch (\Exception $e) {
            // Continuer l'inscription sans géolocalisation
            error_log('Geolocation unavailable: ' . $e->getMessage());
            
            $userData = [
                'email' => $email,
                'registration_ip' => $ip,
            ];
        }
    }
}
```

### Cas 3 : Gestion de la localisation avec cache

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\IpGeolocationService;

class CachedGeolocationService
{
    private IpGeolocationService $service;
    private array $cache = [];
    
    public function __construct()
    {
        $this->service = new IpGeolocationService();
    }
    
    public function getLocation(string $ip): array
    {
        if (isset($this->cache[$ip])) {
            return $this->cache[$ip];
        }
        
        try {
            $result = $this->service->locate($ip);
            
            $this->cache[$ip] = [
                'success' => true,
                'country' => $result->country,
                'city' => $result->city,
                'isp' => $result->isp,
                'lat' => $result->lat,
                'lon' => $result->lon,
            ];
            
        } catch (\Exception $e) {
            $this->cache[$ip] = [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
        
        return $this->cache[$ip];
    }
}
```

### Cas 4 : Récupérer des informations pour un dashboard

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\IpGeolocationService;

$service = new IpGeolocationService();
$ips = ['8.8.8.8', '1.1.1.1', '169.159.220.210'];

$countries = [];

foreach ($ips as $ip) {
    try {
        $result = $service->locate($ip);
        $countries[$ip] = $result->country;
    } catch (\Exception $e) {
        $countries[$ip] = 'Unknown';
    }
}

print_r($countries);
// [
//     '8.8.8.8' => 'United States',
//     '1.1.1.1' => 'United States',
//     '169.159.220.210' => 'Congo (DRC)',
// ]
```

---

## Gestion des erreurs

| Situation | Exception | Message |
|-----------|-----------|---------|
| IP vide | `InvalidArgumentException` | `IP address cannot be empty.` |
| IP invalide | `InvalidArgumentException` | `Invalid IP address: {ip}` |
| Échec de l'API ip-api.com | `RuntimeException` | `IP geolocation failed: {error}` |
| Réponse invalide | `RuntimeException` | `IP geolocation returned invalid data` |
| Timeout | `RuntimeException` | `cURL error 28: Operation timed out` |
| Erreur HTTP 500 | `RuntimeException` | `IP geolocation failed: Internal Server Error` |

## Intégration

Le service s'intègre avec les composants suivants :

- **`ClientService`** : Client HTTP pour les requêtes API
- **`IpGeoRequest`** : Requête HTTP typée
- **`IpGeoResponse`** : Réponse HTTP typée
- **`IpGeoSuccessStruct`** : Structure de données succès
- **`IpGeoFailureStruct`** : Structure de données échec

## Performance

- **Temps de réponse** : ~200-500ms (dépend de l'API)
- **Timeouts** : Connect 5s, Read 20s
- **Cache recommandé** : À implémenter côté utilisateur
- **Rate limit** : 45 requêtes/minute (ip-api.com gratuit)

## Compatibilité

| Version PHP | Support |
|-------------|---------|
| PHP 8.2+ | ✅ Complet |
| PHP 8.1 | ✅ Complet |
| PHP 8.0 | ✅ Complet |

## Exemple complet

```php
<?php

declare(strict_types=1);

use AndyDefer\PhpServices\Services\IpGeolocationService;

$service = new IpGeolocationService();

// IP de test (Google DNS)
$ip = '8.8.8.8';

try {
    $result = $service->locate($ip);
    
    echo "=== Informations de géolocalisation ===\n";
    echo "IP: {$result->query}\n";
    echo "Pays: {$result->country} ({$result->countryCode})\n";
    echo "Région: {$result->regionName} ({$result->region})\n";
    echo "Ville: {$result->city}\n";
    echo "Code postal: {$result->zip}\n";
    echo "Coordonnées: {$result->lat}, {$result->lon}\n";
    echo "Fuseau horaire: {$result->timezone}\n";
    echo "FAI: {$result->isp}\n";
    echo "Organisation: {$result->org}\n";
    echo "AS: {$result->as}\n";
    
} catch (\Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}

// Exemple avec gestion manuelle
$response = $service->locateRaw('1.1.1.1');

if ($response->isSuccess()) {
    $data = $response->getSuccessData();
    echo "\n✅ Localisation réussie !\n";
    echo "Ville: {$data->city}\n";
    echo "Pays: {$data->country}\n";
} else {
    echo "\n❌ Échec: " . $response->getErrorMessage() . "\n";
}
```

## Voir aussi

- `IpGeoRequest` - Requête HTTP pour la géolocalisation
- `IpGeoResponse` - Réponse HTTP typée
- `IpGeoSuccessStruct` - Structure de données succès
- `IpGeoFailureStruct` - Structure de données échec
- [Documentation officielle ip-api.com](https://ip-api.com/docs/api:json)
---
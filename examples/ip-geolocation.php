<?php

declare(strict_types=1);

require './vendor/autoload.php';

use AndyDefer\PhpServices\Services\IpGeolocationService;

// ============================================================
// EXEMPLE 1 : Avec le service (recommandé)
// ============================================================
echo "=== IP Geolocation - Exemples ===\n\n";

echo "1. Avec le service (recommandé):\n";

$service = new IpGeolocationService;

try {
    // IP publique (Google DNS)
    $ip = '8.8.8.8';
    $result = $service->locate($ip);

    echo "✅ IP localisée avec succès !\n";
    echo "IP: {$result->query}\n";
    echo "Pays: {$result->country} ({$result->countryCode})\n";
    echo "Région: {$result->regionName} ({$result->region})\n";
    echo "Ville: {$result->city}\n";
    echo "Code postal: {$result->zip}\n";
    echo "Coordonnées: {$result->lat}, {$result->lon}\n";
    echo "Fuseau horaire: {$result->timezone}\n";
    echo "FAI: {$result->isp}\n";
    echo "Organisation: {$result->org}\n";
    echo "AS: {$result->as}\n\n";

} catch (Exception $e) {
    echo '❌ Erreur: '.$e->getMessage()."\n\n";
}

// ============================================================
// EXEMPLE 2 : Avec méthode raw (gestion manuelle)
// ============================================================
echo "2. Avec méthode raw (gestion manuelle):\n";

try {
    $ip = '1.1.1.1';
    $response = $service->locateRaw($ip);

    if ($response->isSuccess()) {
        $data = $response->getSuccessData();
        echo "✅ Succès !\n";
        echo "IP: {$data->query}\n";
        echo "Pays: {$data->country} ({$data->countryCode})\n";
        echo "Ville: {$data->city}\n";
        echo "FAI: {$data->isp}\n\n";
    } else {
        echo '❌ Échec: '.$response->getErrorMessage()."\n";
        echo 'IP testée: '.$response->getQuery()."\n\n";
    }

} catch (Exception $e) {
    echo '❌ Erreur: '.$e->getMessage()."\n\n";
}

// ============================================================
// EXEMPLE 3 : IP de la RDC (Kinshasa)
// ============================================================
echo "3. IP de la RDC (Kinshasa):\n";

try {
    // IP de Kinshasa (Vodacom Congo)
    $ip = '169.159.220.210';
    $result = $service->locate($ip);

    echo "✅ IP localisée avec succès !\n";
    echo "IP: {$result->query}\n";
    echo "Pays: {$result->country} ({$result->countryCode})\n";
    echo "Région: {$result->regionName} ({$result->region})\n";
    echo "Ville: {$result->city}\n";
    echo "FAI: {$result->isp}\n";
    echo "Organisation: {$result->org}\n";
    echo "AS: {$result->as}\n";
    echo "Coordonnées: {$result->lat}, {$result->lon}\n";
    echo "Fuseau horaire: {$result->timezone}\n\n";

} catch (Exception $e) {
    echo '❌ Erreur: '.$e->getMessage()."\n\n";
}

// ============================================================
// EXEMPLE 4 : IP invalide (gestion d'erreur)
// ============================================================
echo "4. IP invalide (gestion d'erreur):\n";

try {
    $ip = '169.159.220.210ddd';
    $result = $service->locate($ip);

    echo "✅ IP localisée !\n";
    echo "IP: {$result->query}\n";
    echo "Pays: {$result->country}\n";

} catch (Exception $e) {
    echo '❌ Erreur: '.$e->getMessage()."\n\n";
}

// ============================================================
// EXEMPLE 5 : Récupération des informations utilisateur
// ============================================================
echo "5. Récupération des informations utilisateur:\n";

function getUserLocation(string $ip): array
{
    $service = new IpGeolocationService;

    try {
        $result = $service->locate($ip);

        return [
            'success' => true,
            'country' => $result->country,
            'countryCode' => $result->countryCode,
            'city' => $result->city,
            'region' => $result->regionName,
            'isp' => $result->isp,
            'latitude' => $result->lat,
            'longitude' => $result->lon,
            'timezone' => $result->timezone,
            'ip' => $result->query,
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }
}

// Tester avec une IP réelle
$userInfo = getUserLocation('169.159.220.210');

if ($userInfo['success']) {
    echo "✅ Informations utilisateur récupérées :\n";
    echo '  Pays: '.$userInfo['country'].' ('.$userInfo['countryCode'].")\n";
    echo '  Ville: '.$userInfo['city']."\n";
    echo '  Région: '.$userInfo['region']."\n";
    echo '  FAI: '.$userInfo['isp']."\n";
    echo '  Coordonnées: '.$userInfo['latitude'].', '.$userInfo['longitude']."\n";
    echo '  Fuseau horaire: '.$userInfo['timezone']."\n";
    echo '  IP: '.$userInfo['ip']."\n";
} else {
    echo '❌ Erreur: '.$userInfo['error']."\n";
}

echo "\n✅ Tous les exemples sont terminés !\n";

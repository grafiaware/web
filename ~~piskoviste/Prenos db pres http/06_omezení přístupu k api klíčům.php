<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

//Omezení přístupů k API klíčům můžeš implementovat několika způsoby, například:

//IP Whitelisting – API klíč může být použit pouze z určité IP adresy.
//Rate Limiting – Omezíme počet požadavků za určitou dobu.
//Přístupové role a oprávnění – Každý API klíč bude mít oprávnění jen k určitým akcím.
//CORS ochrana – Omezíme přístup API pouze na povolené domény.
//Níže jsou implementace jednotlivých omezení:

//1. IP Whitelisting
//Umožníme API klíči fungovat pouze z předem definovaných IP adres.
//Nejprve přidáme sloupec allowed_ips do databáze:

//sql
//ALTER TABLE api_keys ADD COLUMN allowed_ips TEXT NULL;
//Poté při validaci API klíče zkontrolujeme IP adresu:

function validateApiKeyWithIP($apiKey, $userIp, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM api_keys WHERE api_key = ? AND revoked = 0 AND (expires_at IS NULL OR expires_at > NOW())");
    $stmt->execute([$apiKey]);
    $apiKeyData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$apiKeyData) {
        return false;
    }

    // Pokud nejsou žádná omezení na IP, klíč je platný
    if (empty($apiKeyData['allowed_ips'])) {
        return true;
    }

    $allowedIps = explode(',', $apiKeyData['allowed_ips']);
    return in_array($userIp, $allowedIps);
}

// Použití
$userIp = $_SERVER['REMOTE_ADDR'];
if (validateApiKeyWithIP($apiKey, $userIp, $pdo)) {
    echo "API klíč je platný z IP: $userIp";
} else {
    echo "Přístup odepřen pro IP: $userIp";
}
//2. Rate Limiting (Omezení počtu požadavků)
//Každý API klíč bude mít maximální počet požadavků za určitou dobu (např. 1000 za hodinu).
//
//Přidáme tabulku pro sledování požadavků:
//
//sql
//Zkopírovat
//Upravit
//CREATE TABLE api_usage (
//    id INT AUTO_INCREMENT PRIMARY KEY,
//    api_key VARCHAR(64) NOT NULL,
//    request_count INT DEFAULT 0,
//    last_request TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//);
//Funkce pro kontrolu počtu požadavků:

function checkRateLimit($apiKey, $pdo, $limit = 1000, $timeframe = 3600) {
    $stmt = $pdo->prepare("SELECT request_count, last_request FROM api_usage WHERE api_key = ?");
    $stmt->execute([$apiKey]);
    $usage = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usage) {
        $stmt = $pdo->prepare("INSERT INTO api_usage (api_key, request_count) VALUES (?, 1)");
        $stmt->execute([$apiKey]);
        return true;
    }

    $timeSinceLastRequest = time() - strtotime($usage['last_request']);

    if ($timeSinceLastRequest > $timeframe) {
        $stmt = $pdo->prepare("UPDATE api_usage SET request_count = 1, last_request = NOW() WHERE api_key = ?");
        $stmt->execute([$apiKey]);
        return true;
    }

    if ($usage['request_count'] >= $limit) {
        return false;
    }

    $stmt = $pdo->prepare("UPDATE api_usage SET request_count = request_count + 1 WHERE api_key = ?");
    $stmt->execute([$apiKey]);
    return true;
}

// Použití
if (!checkRateLimit($apiKey, $pdo)) {
    echo "Překročen limit požadavků, zkuste to později.";
    exit;
}

//3. Přístupové role a oprávnění
//Každý API klíč může mít definovaná oprávnění (např. pouze read, write, admin).

//Nejprve přidáme sloupec do tabulky:

//ALTER TABLE api_keys ADD COLUMN permissions ENUM('read', 'write', 'admin') NOT NULL DEFAULT 'read';
//Pak při validaci zkontrolujeme oprávnění:

function checkPermission($apiKey, $requiredPermission, $pdo) {
    $stmt = $pdo->prepare("SELECT permissions FROM api_keys WHERE api_key = ? AND revoked = 0");
    $stmt->execute([$apiKey]);
    $apiKeyData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$apiKeyData) {
        return false;
    }

    $permissionLevels = ['read' => 1, 'write' => 2, 'admin' => 3];

    return $permissionLevels[$apiKeyData['permissions']] >= $permissionLevels[$requiredPermission];
}

// Použití
if (!checkPermission($apiKey, 'write', $pdo)) {
    echo "Nemáte oprávnění k této akci.";
    exit;
}
//4. CORS ochrana (omezení na domény)
//Pokud chceš, aby API klíče byly použitelné jen z určitých webových stránek, můžeš přidat CORS pravidla:

$allowedOrigins = ['https://mojedomena.cz', 'https://partner.cz'];

if (!in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("HTTP/1.1 403 Forbidden");
    echo "Přístup zamítnut.";
    exit;
}

header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

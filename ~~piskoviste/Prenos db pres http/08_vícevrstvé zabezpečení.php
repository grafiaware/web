<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

//Vícevrstvé zabezpečení znamená kombinaci několika bezpečnostních opatření, která ztěžují zneužití API klíčů. Doporučuji následující vrstvy:
//
//1. Šifrování API klíčů (Již Implementováno)
//Používáme AES-256-CBC pro bezpečné uložení API klíčů v databázi.
//
//✅ Šifrované klíče: Ukládají se šifrovaně v databázi, takže ani útočník s přístupem k databázi je nemůže přímo použít.
//✅ Bezpečnostní klíč ($secretKey): Ukládá se v .env souboru nebo bezpečném trezoru.
//
//2. Dvoufázová autorizace API klíčů
//Přidáme tajný podpis (HMAC), který zajistí, že API požadavek není podvržený.
//
//Generování API klíče s HMAC
//Při vytváření klíče vygenerujeme tajný HMAC podpis:
//
//php
//Zkopírovat
//Upravit
function generateHmacSignature($apiKey, $secret) {
    return hash_hmac('sha256', $apiKey, $secret);
}
//Při každém požadavku klient přidá HMAC podpis a my ho ověříme:
//
//php
//Zkopírovat
//Upravit
function validateHmacSignature($apiKey, $providedHmac, $secret) {
    $expectedHmac = generateHmacSignature($apiKey, $secret);
    return hash_equals($expectedHmac, $providedHmac); // Bezpečné porovnání
}
//Jak to funguje v API?
//Klient odešle API klíč a HMAC podpis v hlavičce:
//makefile
//Zkopírovat
//Upravit
//Authorization: Bearer <API_KEY>
//X-Signature: <HMAC_SIGNATURE>
//Server ověří, zda HMAC podpis odpovídá klíči.
//✅ Zabraňuje MITM útokům: Útočník nemůže jen tak zkopírovat API klíč – musí znát i tajný podpis.

//3. Rate Limiting a Detekce Podezřelého Chování
//Každý klíč bude mít limit požadavků, např. 1000 požadavků za hodinu.
//
//Přidáme sloupec do databáze pro sledování požadavků:
//
//sql
//Zkopírovat
//Upravit
//ALTER TABLE api_keys ADD COLUMN request_count INT DEFAULT 0;
//ALTER TABLE api_keys ADD COLUMN last_request TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
//Pak při každém požadavku aktualizujeme počet:
//
//php
//Zkopírovat
//Upravit
function enforceRateLimit($apiKey, $pdo, $limit = 1000, $timeframe = 3600) {
    $stmt = $pdo->prepare("SELECT request_count, last_request FROM api_keys WHERE api_key = ?");
    $stmt->execute([$apiKey]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) return false;

    $timeSinceLastRequest = time() - strtotime($data['last_request']);

    if ($timeSinceLastRequest > $timeframe) {
        $stmt = $pdo->prepare("UPDATE api_keys SET request_count = 1, last_request = NOW() WHERE api_key = ?");
        $stmt->execute([$apiKey]);
        return true;
    }

    if ($data['request_count'] >= $limit) {
        return false; // Limit překročen
    }

    $stmt = $pdo->prepare("UPDATE api_keys SET request_count = request_count + 1 WHERE api_key = ?");
    $stmt->execute([$apiKey]);

    return true;
}

//4. Omezení přístupu podle IP adresy
//Povolíme API klíče jen pro určité IP adresy.
//
//V databázi přidáme:
//
//sql
//Zkopírovat
//Upravit
//ALTER TABLE api_keys ADD COLUMN allowed_ips TEXT NULL;
//A při ověřování klíče kontrolujeme:
//
//php
//Zkopírovat
//Upravit
function checkIpRestriction($apiKey, $userIp, $pdo) {
    $stmt = $pdo->prepare("SELECT allowed_ips FROM api_keys WHERE api_key = ?");
    $stmt->execute([$apiKey]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || empty($row['allowed_ips'])) return true; // Pokud nejsou omezení, klíč je platný

    $allowedIps = explode(',', $row['allowed_ips']);
    return in_array($userIp, $allowedIps);
}

//5. Automatická Rotace API klíčů
//Každý klíč bude mít expirační datum (např. 30 dní) a po uplynutí platnosti se automaticky vygeneruje nový.
//
//V databázi přidáme sloupec:
//
//sql
//Zkopírovat
//Upravit
//ALTER TABLE api_keys ADD COLUMN expires_at TIMESTAMP NULL;
//Pak vytvoříme funkci, která automaticky obnoví API klíč:
//
//php
//Zkopírovat
//Upravit
function autoRotateApiKey($userId, $pdo, $secretKey) {
    $stmt = $pdo->prepare("SELECT expires_at FROM api_keys WHERE user_id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && strtotime($row['expires_at']) < time()) {
        // API klíč expiroval, vytvoříme nový
        return storeEncryptedApiKey($userId, $pdo, $secretKey);
    }

    return false;
}

//6. Blokace podezřelých IP adres (Brute Force Protection)
//Pokud API klíč selže příliš často, můžeme blokovat IP adresu.
//
//Vytvoříme tabulku pro sledování neúspěšných pokusů:
//
//sql
//Zkopírovat
//Upravit
//CREATE TABLE blocked_ips (
//    ip VARCHAR(45) PRIMARY KEY,
//    failed_attempts INT DEFAULT 0,
//    blocked_until TIMESTAMP NULL
//);
//Při ověřování API klíče aktualizujeme pokusy:
//
//php
//Zkopírovat
//Upravit
function blockSuspiciousIp($ip, $pdo) {
    $stmt = $pdo->prepare("SELECT failed_attempts, blocked_until FROM blocked_ips WHERE ip = ?");
    $stmt->execute([$ip]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data && $data['blocked_until'] && strtotime($data['blocked_until']) > time()) {
        return false; // IP je blokovaná
    }

    if (!$data) {
        $stmt = $pdo->prepare("INSERT INTO blocked_ips (ip, failed_attempts) VALUES (?, 1)");
    } else {
        $failedAttempts = $data['failed_attempts'] + 1;
        $blockedUntil = ($failedAttempts >= 5) ? date('Y-m-d H:i:s', strtotime('+15 minutes')) : null;

        $stmt = $pdo->prepare("UPDATE blocked_ips SET failed_attempts = ?, blocked_until = ? WHERE ip = ?");
        $stmt->execute([$failedAttempts, $blockedUntil, $ip]);
    }

    return true;
}

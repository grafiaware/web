<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

//Níže je implementace generování a rotace API klíčů v PHP:

//1. Vytvoření tabulky v databázi
//Nejprve potřebujeme tabulku pro ukládání API klíčů. Použij MySQL:
//
//sql
//CREATE TABLE api_keys (
//    id INT AUTO_INCREMENT PRIMARY KEY,
//    user_id INT NOT NULL,
//    api_key VARCHAR(64) NOT NULL UNIQUE,
//    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    expires_at TIMESTAMP NULL,
//    revoked BOOLEAN DEFAULT FALSE
//)

//2. Generování API klíče
//Použijeme bezpečnou metodu bin2hex(random_bytes(32)):
//
function generateApiKey() {
    return bin2hex(random_bytes(32)); // 64 znaků dlouhý API klíč
}
//3. Uložení API klíče do databáze
//Přiřadíme API klíč uživateli a nastavíme datum expirace (např. 30 dní):
function storeApiKey($userId, $pdo) {
    $apiKey = generateApiKey();
    $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

    $stmt = $pdo->prepare("INSERT INTO api_keys (user_id, api_key, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $apiKey, $expiresAt]);

    return $apiKey;
}
//4. Ověření API klíče
//Zkontrolujeme, zda API klíč existuje, není expirovaný a není zneplatněný:
function validateApiKey($apiKey, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM api_keys WHERE api_key = ? AND revoked = 0 AND (expires_at IS NULL OR expires_at > NOW())");
    $stmt->execute([$apiKey]);
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}
//5. Rotace API klíče
//Rotace znamená, že vygenerujeme nový klíč a starý označíme jako zneplatněný:
function rotateApiKey($userId, $pdo) {
    $newApiKey = generateApiKey();
    $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

    // Zneplatníme starý API klíč
    $stmt = $pdo->prepare("UPDATE api_keys SET revoked = 1 WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Uložíme nový API klíč
    $stmt = $pdo->prepare("INSERT INTO api_keys (user_id, api_key, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $newApiKey, $expiresAt]);

    return $newApiKey;
}
//6. Použití ve skriptu
//Příklad, jak vytvořit a ověřit API klíč:
// Připojení k databázi
$pdo = new PDO('mysql:host=localhost;dbname=my_database', 'username', 'password');

// Generování nového API klíče
$userId = 1; // ID uživatele
$apiKey = storeApiKey($userId, $pdo);
echo "Generated API Key: $apiKey\n";

// Ověření API klíče
if (validateApiKey($apiKey, $pdo)) {
    echo "API klíč je platný\n";
} else {
    echo "API klíč je neplatný nebo expirovaný\n";
}

// Rotace API klíče
$newApiKey = rotateApiKey($userId, $pdo);
echo "New API Key after rotation: $newApiKey\n";

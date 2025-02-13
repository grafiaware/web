<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

//Pro šifrování API klíčů v databázi můžeš použít OpenSSL nebo bcrypt. Doporučený přístup je šifrování pomocí OpenSSL, protože umožňuje dešifrování, pokud ho budeš potřebovat.
//
//1. Vytvoření tabulky s šifrovanými klíči
//Přidáme sloupec pro šifrovaný API klíč:
//
//sql
//Zkopírovat
//Upravit
//ALTER TABLE api_keys ADD COLUMN encrypted_api_key TEXT NOT NULL;
//2. Šifrování API klíče pomocí OpenSSL
//Použijeme AES-256-CBC pro silné šifrování:
//
//php
//Zkopírovat
//Upravit
function encryptApiKey($apiKey, $secretKey) {
    $iv = random_bytes(openssl_cipher_iv_length('AES-256-CBC'));
    $encrypted = openssl_encrypt($apiKey, 'AES-256-CBC', $secretKey, 0, $iv);
    return base64_encode($iv . $encrypted); // Ukládáme IV a šifrovaný text dohromady
}
//3. Dešifrování API klíče
//Když budeme potřebovat ověřit klíč, dešifrujeme ho:
//
//php
//Zkopírovat
//Upravit
function decryptApiKey($encryptedApiKey, $secretKey) {
    $data = base64_decode($encryptedApiKey);
    $iv_length = openssl_cipher_iv_length('AES-256-CBC');
    $iv = substr($data, 0, $iv_length);
    $encrypted = substr($data, $iv_length);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $secretKey, 0, $iv);
}
//4. Ukládání šifrovaného API klíče do databáze
//Když generujeme nový klíč, uložíme ho šifrovaně:
//
//php
//Zkopírovat
//Upravit
function storeEncryptedApiKey($userId, $pdo, $secretKey) {
    $apiKey = bin2hex(random_bytes(32)); // Generujeme náhodný API klíč
    $encryptedApiKey = encryptApiKey($apiKey, $secretKey);

    $stmt = $pdo->prepare("INSERT INTO api_keys (user_id, encrypted_api_key) VALUES (?, ?)");
    $stmt->execute([$userId, $encryptedApiKey]);

    return $apiKey; // Vracíme čistý API klíč pouze při generování
}
//5. Ověření API klíče
//Při každém požadavku dešifrujeme klíč a porovnáme ho s poskytnutým:
//
//php
//Zkopírovat
//Upravit
function validateEncryptedApiKey($providedKey, $pdo, $secretKey) {
    $stmt = $pdo->prepare("SELECT encrypted_api_key FROM api_keys WHERE revoked = 0");
    $stmt->execute();
    $apiKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($apiKeys as $row) {
        if (decryptApiKey($row['encrypted_api_key'], $secretKey) === $providedKey) {
            return true; // API klíč je platný
        }
    }

    return false; // Neplatný API klíč
}
//6. Použití v aplikaci
//php
//Zkopírovat
//Upravit
$pdo = new PDO('mysql:host=localhost;dbname=my_database', 'username', 'password');
$secretKey = 'super_secret_32_byte_key'; // Ulož bezpečně, např. v .env souboru

// Generování a ukládání šifrovaného klíče
$userId = 1;
$apiKey = storeEncryptedApiKey($userId, $pdo, $secretKey);
echo "Generated API Key: $apiKey\n";

// Ověření klíče
if (validateEncryptedApiKey($apiKey, $pdo, $secretKey)) {
    echo "API klíč je platný\n";
} else {
    echo "API klíč je neplatný\n";
}


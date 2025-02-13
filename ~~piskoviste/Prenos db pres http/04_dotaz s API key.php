<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

//Při odesílání požadavků z serveru B na server A přidejte API klíč do hlavičky HTTP požadavku. To lze provést pomocí funkce file_get_contents() s kontextem nebo pomocí knihovny Guzzle.
//
//Příklad použití file_get_contents() s kontextem:
    
// URL skriptu na serveru A
$url = 'http://serverA.com/export.php';

// API klíč
$apiKey = 'váš_api_klíč';

// Vytvoření kontextu pro GET požadavek s hlavičkou Authorization
$options = [
    'http' => [
        'method'  => 'GET',
        'header'  => "Authorization: Bearer $apiKey",
    ],
];
$context = stream_context_create($options);

// Načtení obsahu URL s použitím kontextu
$response = file_get_contents($url, false, $context);

// Kontrola, zda došlo k chybě
if ($response === false) {
    die('Chyba při získávání dat.');
}

// Dekódování JSON dat
$data = json_decode($response, true);

// Připojení k databázi na serveru B
$pdo = new PDO('mysql:host=localhost;dbname=databaze', 'uzivatel', 'heslo');

// Příprava dotazu pro vložení dat
$stmt = $pdo->prepare('INSERT INTO tabulka (sloupec1, sloupec2) VALUES (?, ?)');

// Vložení každého záznamu
foreach ($data as $row) {
    $stmt->execute([$row['sloupec1'], $row['sloupec2']]);
}

//Na serveru A implementujte logiku pro ověření přijatého API klíče v hlavičce požadavku. Pokud klíč není platný nebo chybí, server by měl vrátit odpovídající chybovou hlášku (např. HTTP status kód 401 Unauthorized).
//
//Příklad ověření API klíče na serveru A:
    
// Předpokládáme, že $validApiKey obsahuje platný API klíč
$validApiKey = 'váš_api_klíč';

// Získání API klíče z hlavičky Authorization
$headers = getallheaders();
if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    // Očekáváme formát "Bearer {api_key}"
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $apiKey = $matches[1];
        if ($apiKey === $validApiKey) {
            // API klíč je platný, pokračujte ve zpracování požadavku
            // ...
        } else {
            // Neplatný API klíč
            http_response_code(401);
            echo 'Neplatný API klíč.';
            exit;
        }
    } else {
        // Chybí nebo nesprávný formát Authorization hlavičky
        http_response_code(400);
        echo 'Chybí nebo nesprávný formát Authorization hlavičky.';
        exit;
    }
} else {
    // Chybí Authorization hlavička
    http_response_code(400);
    echo 'Chybí Authorization hlavička.';
    exit;
}
//Rotace API klíčů: Pravidelně měňte API klíče a implementujte mechanismus pro jejich rotaci, aby se minimalizovalo riziko zneužití v případě kompromitace.    
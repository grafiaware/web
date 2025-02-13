<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

// GET

// URL skriptu na serveru A
$url = 'http://serverA.com/export.php';

// Načtení obsahu URL
$response = file_get_contents($url);

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

// POST s x-www-form-urlencoded

// URL skriptu na serveru A
$url = 'http://serverA.com/export.php';

// Data, která chcete odeslat
$data = [
    'param1' => 'hodnota1',
    'param2' => 'hodnota2',
];

// Vytvoření kontextu pro POST požadavek
$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($data),
    ],
];
$context = stream_context_create($options);

// Načtení obsahu URL s použitím kontextu
$response = file_get_contents($url, false, $context);

// Kontrola, zda došlo k chybě
if ($response === false) {
    die('Chyba při odesílání dat.');
}

// Zpracování odpovědi
// ...

// POST s application/json

// URL skriptu na serveru A
$url = 'http://serverA.com/export.php';

// Data, která chcete odeslat
$data = [
    'param1' => 'hodnota1',
    'param2' => 'hodnota2',
];

// Vytvoření kontextu pro POST požadavek
$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => 'Content-Type: application/json',
        'content' => json_encode($data),
    ],
];
$context = stream_context_create($options);

// Načtení obsahu URL s použitím kontextu
$response = file_get_contents($url, false, $context);

// Kontrola, zda došlo k chybě
if ($response === false) {
    die('Chyba při odesílání dat.');
}

// Zpracování odpovědi
// ...
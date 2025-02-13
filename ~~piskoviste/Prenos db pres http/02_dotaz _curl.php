<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

// URL skriptu na serveru A
$url = 'http://serverA.com/export.php';

// Inicializace cURL
$ch = curl_init($url);

// Nastavení cURL pro získání dat
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Provedení požadavku
$response = curl_exec($ch);

// Zavření cURL
curl_close($ch);

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
?>

<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

// Připojení k databázi
$pdo = new PDO('mysql:host=localhost;dbname=databaze', 'uzivatel', 'heslo');

// Příprava dotazu
$query = $pdo->query('SELECT * FROM tabulka');

// Získání všech řádků jako pole
$data = $query->fetchAll(PDO::FETCH_ASSOC);

// Odeslání hlavičky pro JSON
header('Content-Type: application/json');

// Odeslání dat jako JSON
echo json_encode($data);
?>

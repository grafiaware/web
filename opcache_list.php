<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

$status = opcache_get_status(true);

// vypíše skripty, které nikdy neměly hit.
foreach ($status['scripts'] as $file => $info) {
    echo "<pre>";
    if ($info['hits'] == 0) {
        echo "Podezřelý: $file\n";
    }
    echo "</pre>";
}


<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function friendly_url($nadpis) {
    $url = $nadpis;
    $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
    $url = trim($url, "-");
    $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
    $url = strtolower($url);
    $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
    return $url;
}

//Příklad - zachová rest parametry a jen přilepí friendly url
//
//RewriteRule ^profile/([0-9]+)/([A-Za-z0-9-]+)/?$ index.php?p=profile&id=$1
//
//Should work for :
//
//www.domain.com/index.php?p=profile&id=20
//
//to
//
//www.domain.com/profile/20/profile-friendly-name

###########################

//Doporučení
//
//    Při pouhém přejmenování původní URL na novu použít 301.
//    Při odstranění původní stránky a vytvoření nové stránky s podobným obsahem, přesměrovat pomocí 301.
//    Při odstranění původní URL, ale není současně nový, adekvátní obsah, nepřesměrovávat (ani na úvodní stranu).

$trans = [
    'úvod',
    'Úvod'
];

foreach ($trans as $tr) {
    echo "<pre>".$tr.' -> '. friendly_url($tr)."</pre>";
}

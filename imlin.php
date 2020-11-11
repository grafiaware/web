<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$a = [
        0 => '/www/item/cs/5fa8fae29e147',
        1 => 'cs',
        2 => '5fa8fae29e147'
    ];

$p = print_r($a, true);
$t = str_replace(' ', '', $p);
$i = str_replace(chr(10), '', $t);
$m = str_replace('[', ' [', $i);
$a= 1;
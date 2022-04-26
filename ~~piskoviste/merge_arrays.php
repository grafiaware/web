<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

$a = ['name1'];
$b = ['name2', 'name3'];

$m = array_merge($a, $b);
$p = $a + $b;

$ka = array_flip($a);
$kb = array_flip($b);
$km = array_merge($ka, $kb);
$kp = $ka + $kb;
$f_ka = array_flip($ka);
$f_kb = array_flip($kb);

$x = 1;
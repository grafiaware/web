<?php
$length = 8;
$emailPattern = "(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{".$length.",}";
//Heslo musí obsahovat nejméně jedno velké písmeno, jedno malé písmeno a jednu číslici. Jiné znaky než písmena a číslice nejsou povoleny. Délka musí být nejméně 8 znaků.";

$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';  // bez nuly
$count = 0;
do {
$psw = substr(str_shuffle(str_repeat($chars,$length)),0,$length);
$ok = preg_match("/$emailPattern/", $psw);
$count++;
} while (!$ok);

echo "<p>$count</p><p>$psw</p>";
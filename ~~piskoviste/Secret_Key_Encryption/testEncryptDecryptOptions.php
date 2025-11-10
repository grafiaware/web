<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
$data ="+ěščřžýáíé=+ĚŠČŘŽÝÁÍÉ=qwertzuiopasdfghjklyxcvbnmQWERTZUIOPASDFGHJKLYXCVBNM?_-;";
$key= 'ěščřžýáíé';
// Hash klíče
$passphrase = openssl_digest($key, 'sha256', true);
$encrypted = openssl_encrypt($data, 'aes-256-cbc', $passphrase, 0);

$decrypted = openssl_decrypt($encrypted, 'sha256', $passphrase);
$decrypted = openssl_decrypt($encrypted, $this->cipherMethod, $keyhash, $this->options, $iv);
$shoda = ($data === $decrypted);
$a=0;
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$texts[] = 'alert("Nee.")';
$texts[] = "alert('Nee.')";
$texts[] = 'confirm("EEEEEtfdcfcz" );';
$texts[] = 'if(window.confirm("EEEEEtfdcfcz")) { alert("Nee."); }';
$texts[] = 'var c=confirm("EEEEEtfdcfcz");  if(c==false) {alert("Nee.");}';
$texts[] = "var c=confirm('EEEEEtfdcfcz');  if(c==false) {alert('Nee.');}";

//$safe_text = wp_check_invalid_utf8( $text );

foreach ($texts as $text) {
    $safe_text = $text;
    $safe_text = htmlspecialchars( $safe_text, ENT_COMPAT );   // konvertuje &"<> na &xxx kódy (ENT_COMPAT Will convert double-quotes and leave single-quotes alone.)
    $safe_text = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes( $safe_text ) );  // stripslashes odstraní escapovací zpětná lomítka (vždy jedno), preg vymění &#039; a &#039; (uvozovky a apostrofy) za apostrof
    $safe_text = str_replace( "\r", '', $safe_text );
    $safe_text = str_replace( "\n", '\\n', $safe_text );
//    $safe_text = str_replace( "\n", '\\n', addslashes( $safe_text ) );  to by bylo pro zobrazení v html

    echo $safe_text.PHP_EOL;
    echo "<p onclick=\"$safe_text\">Ťuk</p>".PHP_EOL;
    $txt = '"'.trim((string) $safe_text).'"';
    echo "<p onclick=$txt>Ťuk</p>".PHP_EOL;
//    echo "<p onclick=$safe_text>Ťuk</p>".PHP_EOL;
}

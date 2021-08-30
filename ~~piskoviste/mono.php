<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    function removeDiacritics($text) {
        // i pro multi-byte (napr. UTF-8)
        $prevodni_tabulka = [
          'ä'=>'a',  'Ä'=>'A',  'á'=>'a',  'Á'=>'A',
          'à'=>'a',  'À'=>'A',  'ã'=>'a',  'Ã'=>'A',
          'â'=>'a',  'Â'=>'A',  'č'=>'c',  'Č'=>'C',
          'ć'=>'c',  'Ć'=>'C',  'ď'=>'d',  'Ď'=>'D',
          'ě'=>'e',  'Ě'=>'E',  'é'=>'e',  'É'=>'E',
          'ë'=>'e',  'Ë'=>'E',  'è'=>'e',  'È'=>'E',
          'ê'=>'e',  'Ê'=>'E',  'í'=>'i',  'Í'=>'I',
          'ï'=>'i',  'Ï'=>'I',  'ì'=>'i',  'Ì'=>'I',
          'î'=>'i',  'Î'=>'I',  'ľ'=>'l',  'Ľ'=>'L',
          'ĺ'=>'l',  'Ĺ'=>'L',  'ń'=>'n',  'Ń'=>'N',
          'ň'=>'n',  'Ň'=>'N',  'ñ'=>'n',  'Ñ'=>'N',
          'ó'=>'o',  'Ó'=>'O',  'ö'=>'o',  'Ö'=>'O',
          'ô'=>'o',  'Ô'=>'O',  'ò'=>'o',  'Ò'=>'O',
          'õ'=>'o',  'Õ'=>'O',  'ő'=>'o',  'Ő'=>'O',
          'ř'=>'r',  'Ř'=>'R',  'ŕ'=>'r',  'Ŕ'=>'R',
          'š'=>'s',  'Š'=>'S',  'ś'=>'s',  'Ś'=>'S',
          'ť'=>'t',  'Ť'=>'T',  'ú'=>'u',  'Ú'=>'U',
          'ů'=>'u',  'Ů'=>'U',  'ü'=>'u',  'Ü'=>'U',
          'ù'=>'u',  'Ù'=>'U',  'ũ'=>'u',  'Ũ'=>'U',
          'û'=>'u',  'Û'=>'U',  'ý'=>'y',  'Ý'=>'Y',
          'ž'=>'z',  'Ž'=>'Z',  'ź'=>'z',  'Ź'=>'Z'
        ];

        return strtr($text, $prevodni_tabulka);
    };

$text = '
    Už za týden se spustí <b>akce Týden zdraví - zdravá rodina</b>, která se zaměřuje na to nejdůležitější, co můžeme sami v boji proti chorobám dělat: podporu imunity, prevenci a zdravý životní styl.
V průběhu listopadového týdne <b>od soboty 21. do pátku 27. 11. 2020</b> můžete poprvé shlédnout přednášky a rozhovory s odborníky a inspirovat se produkty a službami na podporu a posílení vlastního zdraví.
';
        $patterns = [
            '/(\s[ksvzouiaKSVZOUIA])\s/',
            '/(\d{1})\.\s(\d{1})/'
        ];
        $replacements = [
           '$1&nbsp;',
             '$1.&nbsp;$2'
        ];
        $mono = preg_replace($patterns, $replacements, trim($text));

        echo "<p>$text</p>";
        echo "<p>$mono</p>";
        echo "<p>". removeDiacritics($text)."</p>";
        echo "<p>".removeDiacritics($mono)."</p>";


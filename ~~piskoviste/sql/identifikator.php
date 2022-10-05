<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

$i[] = "gfdgd";
$i[] = "asdf AS uaa";
$i[] = "tata JOIN tutu";
$i[] = "bubu, baba";
$i[] = "\"uvozovkami obalený string\" JOIN 'apostrofy', `backticked words` AS bati";

foreach ($i as $value) {
    $value = str_replace(',', ' ,', $value);
//    $exploded[] = preg_split('/[,| ]/', $value, -1, PREG_SPLIT_NO_EMPTY);  // If this flag is set, only non-empty pieces will be returned by preg_split().
//    $exploded2[] = preg_split("/[\s]+/", $value, -1, PREG_SPLIT_DELIM_CAPTURE);
//    $explodedAll[] = preg_split('/, /', $value, -1);
//    $wordlist[] = preg_split('/\W/', $value, 0, PREG_SPLIT_NO_EMPTY);
//    $terms[] = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[\s,]+/u", $value, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
//    $backticked[] = preg_split("/[\s,]*`([^']+)`[\s,]*|" . "[\s,]+/u", $value, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    // přidá mezery před čárky, split podle mezer (white char) mimo mezer mezi backticksy
    // => vrací slova, řetězec mezi backticksy je jedno slovo (zachová backticksy), čárka je také slovo
    // bohužel, provede split i podle mezer v uvozovká a apostrofech
    $backticked2[] = preg_split("/[\s]*`([^']+)`[\s]*|" . "[\s]+/u", str_replace(',', ' ,', $value), 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    // tato verze vrací i substringu v uvozovká a apostrofech bez uvozovek a apotrofů
    $backticked3[] = preg_split("/" . "[\s]*\\\"([^\\\"]+)\\\"[\s]*|" . "[\s]*'([^']+)'[\s]*|" . "[\s]*`([^']+)`[\s]*|" . "[\s]+/u", str_replace(',', ' ,', $value), 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

    // https://stackoverflow.com/questions/16476744/exploding-a-string-using-a-regular-expression
//The trick is to match parenthesis before the ,
//
//~          Pattern delimiter
//'
//[^']       All charaters but not a single quote
//++         one or more time in [possessive][1] mode
//'
//|          or
//\([^)]++\) the same with parenthesis
//|          or
//[^,]       All characters but not a comma
//++
//~

    preg_match_all("~'[^']++'|\([^)]++\)|[^ ]++~", $value, $backticked4[]);
    // if you have more than one delimiter like quotes (that are the same for open and close), you can write your pattern like this, using a capture group:
    // explanation:

//    (['#@°])   one character in the class is captured in group 1
//    .*?        any character zero or more time in lazy mode
//    \1         group 1 content

    preg_match_all('~([\'"`]).*?\1|\([^)]+\)|[^ ]+~', $value, $backticked5[]);
}

    $keys = [
        ',',
        'AS',
        'JOIN',
        'LEFT',
        'RIGHT',
        'INNER',
        'OUTER'
    ];

// identifikator může být součástí select nebo from
foreach ($backticked5 as $iKey => $match) {
    $terms = $match[0];
    $parenthesis = $match[1];   // znaky nalezené jako ohraničení (captured in group 1)
    $id = [];
    foreach ($terms as $key=>$term) {
        if ($parenthesis[$key]=="" AND (!in_array($term, $keys))) {
                $trn = trim($term);
                $id[] = "`".trim($trn, "`")."`";
        } else {
            $id[] = $term;
        }
    }
    $clause[] = implode(" ", $id);
}

var_dump($clause);
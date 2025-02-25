<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
function importCsv($filename) {
    $file = new SplFileObject($filename, "r");
    $file->setFlags(SplFileObject::READ_CSV);
    $first = true;
    foreach ($file as $row) {
        if ($first) {
            $headers = array_map(function($val){return iconv("Windows-1250", "UTF-8//IGNORE", $val);}, $row);
            $first = false;
        } else {
            $utf8row = array_map(function($val){return iconv("Windows-1250", "UTF-8//IGNORE", $val);}, $row);
            if ($utf8row[0]) {
                $data[] = array_combine($headers, $utf8row); // Spojí hlavičku s daty
            }
        }
    }
    return $data;
}

function exportCsv($filename, array $data) {
    $file = new SplFileObject($filename, "w");
    $headers = array_keys($data);
    $file->fputcsv($headers);
    foreach ($data as $dataRow) {
        $row = array_map(function($val){return iconv("UTF-8", "Windows-1250", $val);}, $dataRow);
        $file->fputcsv($row);
    }
}




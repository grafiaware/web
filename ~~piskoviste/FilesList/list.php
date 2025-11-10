<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
    $dir = "C://ApacheRoot/";
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    $count = 0;
    foreach($files as $file) {
        if ($count++ > 500) {
            echo "... atd.";
            exit;
        }
        if ($file->isDir()){
            echo "<p>".$file->getPathname()."</p>";
        } else {
            echo "<p>-- ".$file->getPathname()."</p>";
        }
    }
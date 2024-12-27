<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

$path = 'myfolder/myimage.png';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);



//The pathinfo($path, PATHINFO_EXTENSION) part might not work with SVG images. Just change the last line to php $base64 = 'data:image/svg+xml;base64,' . base64_encode($data); 
//
//You should use mime_content_type() to get mimetype instead of extension (not reliable)
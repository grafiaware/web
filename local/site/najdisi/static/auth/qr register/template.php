<?php

use Psr\Http\Message\ResponseInterface;

use Auth\Service\Qr\QrImageGenerator;

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

$qrGenerator = new QrImageGenerator();
    $url = 'https://example.com/registrace?qr=' . urlencode($rawToken);
    $png = $qrGenerator->generate($url);
    
    echo "
<div>
<img src=\"data:image/png;base64, ".$png."
</div>
";

//    return new Response(
//        200,
//        ['Content-Type' => 'image/png'],
//        $png
//    );

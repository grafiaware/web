<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace QrLogin\Controllers;

use QrLogin\Services\QRLoginManager;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Description of QRController
 *
 * @author pes2704
 */
class QRController
{
    public function __construct(QRLoginManager $qr) {}      // (private QRLoginManager $qr)

    // returns PNG binary (echo)
    public function qrImage(): void {
        $data = $this->qr->generateLoginUrl();
        $writer = new PngWriter();
        $qr = QrCode::create($data['url']);
        header('Content-Type: image/png');
        echo $writer->write($qr)->getString();
    }

    // ajax poll - expects ?token=...
    public function poll(array $params): array {
        $token = $params['token'] ?? null;
        if (!$token) return ['status'=>'invalid'];
        return $this->qr->validateToken($token);
    }
}
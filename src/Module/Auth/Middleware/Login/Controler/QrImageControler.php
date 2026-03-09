<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Auth\Middleware\Login\Controler;

use Psr\Http\Message\ServerRequestInterface;

use FrontControler\PresentationFrontControlerAbstract;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Access\AccessPresentationInterface;

use Auth\Service\Qr\QrImageGenerator;
use Endroid\QrCode\Writer\Result\ResultInterface;

use Pes\View\View;
use Pes\Text\Html;

/**
 * Description of QrImageControler
 *
 * @author pes2704
 */
class QrImageControler extends PresentationFrontControlerAbstract {
    const QR = [
        'register' => ["https://php8.najdisi.cz/?modal=register0AXR2026", "Registrace na VPV2026"],
    ];
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo, 
            StatusFlashRepo $statusFlashRepo, 
            StatusPresentationRepo $statusPresentationRepo, 
            AccessPresentationInterface $accessPresentation) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $accessPresentation);
    }
    
    ### action metody ###############

    public function qrImage(ServerRequestInterface $request, $qr) {
        $url = self::QR[$qr][0] ?? '';
        $labelText = $url=='' ? 'Chybný parametr!' : self::QR[$qr][1] ??'';
        /** @var QrImageGenerator $qrGenerator */
        $qrGenerator = $this->container->get(QrImageGenerator::class);
        /** @var ResultInterface $result */
        $result = $qrGenerator->generate($url, $labelText);
        $base64 = base64_encode($result->getString());

        $html = Html::tag('div', [],
                    Html::p("Skenuj!")
                    .
                    Html::tag('img', ['src'=>"data:{$result->getMimeType()};charset=utf-8;base64, {$base64}"])
                );

        $response = $this->createStringOKResponse($html);
//        $response->withHeader('Content-Type', $result->getMimeType());
        return $response;
    }
}

<?php
namespace Auth\Service\Qr;
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */
use Endroid\QrCode\Builder\Builder; // verze 6
use Endroid\QrCode\Factory\QrCodeFactory;   // verze 4
use Endroid\QrCode\Writer\PngWriter;
/**
 * Description of QrImageGenerator
 *
 * @author pes2704
 */
final class QrImageGenerator
{
    private $qrCodeFactory;
    private $pngWriter;

    public function __construct(
            QrCodeFactory $qrCodeFactory,
            PngWriter $pngWriter
    ) {
        $this->qrCodeFactory = $qrCodeFactory;
        $this->pngWriter = $pngWriter;
    }
    
    public function generate(string $url): string
    {
//        $result = Builder::create()
        $result = $this->qrCodeFactory->create(
            ->writer(new PngWriter())
            ->data($url)
            ->size(300)
            ->margin(10)
            ->build();

        return $result->getString(); // PNG binary
    }

}

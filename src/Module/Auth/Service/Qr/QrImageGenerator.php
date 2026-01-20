<?php
namespace Auth\Service\Qr;
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

use Endroid\QrCode\Writer\Result\ResultInterface;


/**
 * Description of QrImageGenerator
 *
 * @author pes2704
 */
final class QrImageGenerator
{
    
    public function generate(string $url, $labelText = ''): ResultInterface {
//        $result = Builder::create()
//            ->writer(new PngWriter())
//            ->data($url)
//            ->size(300)
//            ->margin(10)
//            ->build();
        
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
//            logoPath: __DIR__.'/assets/bender.png',
//            logoResizeToWidth: 50,
//            logoPunchoutBackground: true,
            labelText: $labelText,
            labelFont: new OpenSans(20),
            labelAlignment: LabelAlignment::Center
        );

        $result = $builder->build();
        return $result;
    }

}

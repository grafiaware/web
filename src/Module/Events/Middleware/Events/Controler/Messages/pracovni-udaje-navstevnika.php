<?php
//use Red\Model\Entity\VisitorDataPostInterface;
//use VisitorDataPostInterface;
use Events\Model\Entity\VisitorJobRequestInterface;

use Pes\Text\Text;
/** @var VisitorJobRequestInterface  $visitorJobRequest */


?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <base>
        <style type="text/css">html {height:100%;} html, body {width:100%;min-height:100%;} body{word-wrap: break-word;border:0;margin:0;padding:3px;box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; cursor:text; } body::-webkit-scrollbar {width: 10px; height: 10px; background-color: transparent; } body::-webkit-scrollbar-thumb { background-color: #c1c1c1; } body::-webkit-scrollbar-track { background-color: #f5f5f5; border: #f5f5f5; } a:hover {color: #328df7;} a {color: #116cd6; text-decoration: none;}
        </style>
        <style type="text/css" id="mail-body-css">
        </style>
    </head>
    <body class="mcont" contenteditable="true" style="font-size: 13px; font-family: Roboto, 'Segoe UI', Ubuntu, 'lucida grande', tahoma, sans-serif; background-image: none; background-repeat: repeat; background-color: rgb(255, 255, 255); color: rgb(0, 0, 0); background-attachment: fixed; cursor: text; padding: 16px 30px;" id="ext-gen1812">



            <div>
                <img src="<?=$data_logo_grafia?>"  width="70" />                 
                <h2>Veletrh práce a vzdělávání - zájemce o pozici</h2>
                <h2><?= $job->getNazev() ?></h2>

                <!-- <h3>Pracovní údaje návštěvníka - praci.najdisi.cz</h3> -->
                <hr/>
                <br>
                <p> Jméno:&nbsp;&nbsp; <b>
                    <?= Text::esc(
                    trim(
                            $visitorJobRequest->getPrefix()
                            .' '.$visitorJobRequest->getName()
                            .' '.$visitorJobRequest->getSurname()
                            .($visitorJobRequest->getPostfix() ? ', '.$visitorJobRequest->getPostfix() : ''))
                            ); ?>
                    </b></p>
            </div>
            <div><br></div>
            <div>
                <p>e-mail:&nbsp;&nbsp;
                <?= Text::esc($visitorJobRequest->getEmail()); ?></p>
            </div>
            <div><br></div>
            <div>
                <p>Telefon:&nbsp;&nbsp;
                <?= Text::esc($visitorJobRequest->getPhone()); ?></p>
            </div>
            
            <div><br/><br/></div>
            <div>
                <p>Vzdělání, kurzy:</p>
                <div><?= $visitorJobRequest->getCvEducationText(); ?></div>
            </div>
            <div><br/><br/></div>
            <div>
                <p>Pracovní zkušenosti, dovednosti:</p>
                <div><?= $visitorJobRequest->getCvSkillsText(); ?></div>
            </div>
            <div><br/><br/></div>
            
            <div><p>V příloze zasíláme případně přiložený životopis a motivační dopis.</p>
            </div>            
            <div>
                <p style="margin: 0cm 0cm 0pt; padding: 0px; box-sizing: content-box; color: rgb(34, 34, 34);">
                    S pozdravem <br/> tým realizátora Grafia, s.r.o.
                </p>
                <p style="margin: 0cm 0cm 0pt; padding: 0px; box-sizing: content-box; color: rgb(34, 34, 34);">
                    <br>
                </p>
            </div>
        </div>



    </body>
</html>

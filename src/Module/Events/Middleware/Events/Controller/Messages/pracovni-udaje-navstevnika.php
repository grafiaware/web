<?php
use Red\Model\Entity\VisitorDataPostInterface;
/** @var VisitorDataPostInterface $visitorDataPost */
use Pes\Text\Text;
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
                <h1>Veletrh práce a vzdělávání</h1>
                <h2>zájemce o pozici <?= $positionName ?></h2>

                <h3>Pracovní údaje návštěvníka.</h3>
                <br>
                <p>Návštěvník veletrhprace.online</p><p> Jméno:&nbsp;&nbsp; <b>
                    <?= Text::esc(
                    trim(
                            $visitorDataPost->getPrefix()
                            .' '.$visitorDataPost->getName()
                            .' '.$visitorDataPost->getSurname()
                            .($visitorDataPost->getPostfix() ? ', '.$visitorDataPost->getPostfix() : ''))
                            ); ?>
                    </b></p>
            </div>
            <div><br></div>
            <div>
                <p>e-mail</p>
                <p><?= Text::esc($visitorDataPost->getEmail()); ?></p>
            </div>
            <div><br></div>
            <div>
                <p>Telefon</p>
                <p><?= Text::esc($visitorDataPost->getPhone()); ?></p>
            </div>
            <div><br></div>
            <div>
                <p>Vzdělání, kurzy</p>
                <p><?= Text::esc($visitorDataPost->getCvEducationText()); ?></p>
            </div>
            <div><br></div>
            <div>
                <p>Pracovní zkušenosti, dovednosti</p>
                <p><?= Text::esc($visitorDataPost->getCvSkillsText()); ?></p>
            </div>
            <div><br></div>
            <div>V příloze zasíláme případně přiložený životopis a motivační dopis.
            </div>
            <div><br></div>

            <div>
                <p style="margin: 0cm 0cm 0pt; padding: 0px; box-sizing: content-box; color: rgb(34, 34, 34);">Tým Grafia - organizátor veletrhu
                </p>
                <p style="margin: 0cm 0cm 0pt; padding: 0px; box-sizing: content-box; color: rgb(34, 34, 34);">
                    <br>
                </p>
            </div>
        </div>



    </body>
</html>
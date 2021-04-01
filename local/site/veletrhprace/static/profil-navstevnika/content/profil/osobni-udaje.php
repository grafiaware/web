<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

use Site\Configuration;
use Model\Repository\StatusSecurityRepo;

use Middleware\Api\ApiController\VisitorDataUploadControler;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

$accept = implode(", ", Configuration::filesUploadControler()['uploads.acceptedextensions']);
$nameCv = VisitorDataUploadControler::UPLOADED_KEY_CV.$userHash;
$nameLetter = VisitorDataUploadControler::UPLOADED_KEY_LETTER.$userHash;
?>


            <div class="active title">
                <i class="dropdown icon"></i>
                Balíček pracovních údajů
            </div>
            <div class="active content">
                <form class="ui huge form" action="api/v1/event/visitor" method="POST">
                    <!--                        <div class="five wide field">
                                                <div style="background-color: peachpuff; width: 200px; height: 200px; margin: 0 auto;">Foto</div>
                                            </div>-->
                    <div class="four fields">
                        <div class="three wide field">
                            <label>Titul před jménem</label>
                            <input type="text" name="prefix" placeholder="" maxlength="45">
                        </div>
                        <div class="five wide field">
                            <label>Jméno</label>
                            <input type="text" name="name" placeholder="Jméno" maxlength="90">
                        </div>
                        <div class="five wide field">
                            <label>Příjmení</label>
                            <input type="text" name="surname" placeholder="Příjmení" maxlength="90">
                        </div>
                        <div class="three wide field">
                            <label>Titul za jménem</label>
                            <input type="text" name="postfix" placeholder="" maxlength="45">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>E-mail</label>
                            <input type="email" name="email" placeholder="mail@example.cz" maxlength="90">
                        </div>
                        <div class="field">
                            <label>Telefon</label>
                            <input type="tel" name="phone" placeholder="+420 777 8888 555" pattern="(\+420)\s[1-9]\d{2}\s\d{3}\s\d{3}" maxlength="45">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Vzdělání, kurzy</label>
                            <textarea name="cv-education-text" class="working-data"></textarea>
                        </div>
                        <div class="field">
                            <label>Pracovní zkušenosti, dovednosti</label>
                            <textarea name="cv-skills-text" class="working-data"></textarea>
                        </div>
                    </div>

                    <label><b>Nahrané soubory</b></label>
                    <div class="fields">
                        <div class="field">
                            <p>Životopis_Malá.pdf </p>
                        </div>
                        <div class="field">
                                <span class="text maly okraje-horizontal"><a><i class="eye outline icon"></i>Zobrazit soubor</a></span>
                                <span class="text maly okraje-horizontal"><a><i class="trash icon"></i>Smazat</a></span>
                        </div>

                    </div>
                </form>
                <form class="ui huge form" action="api/v1/event/uploadvisitorfile" method="POST" enctype="multipart/form-data">
                     <div class="two fields">
                        <div class="field margin">
                            <label>Příloha - životopis</label>
                            <input type="file" name="<?= $nameCv ?>" accept="<?= $accept ?>"  "multiple"=0 size="1">
                        </div>
                        <div class="field margin">
                            <button class="ui massive primary button" type="submit">Uložit</button>
                        </div>
                     </div>
                </form>
                <form class="ui huge form" action="api/v1/event/uploadvisitorfile" method="POST" enctype="multipart/form-data">
                     <div class="two fields">
                        <div class="field margin">
                            <label>Příloha - motivační dopis</label>
                            <input type="file" name="<?= $nameLetter ?>" accept="<?= $accept ?>"  "multiple"=0 size="1">
                        </div>
                        <div class="field margin">
                            <button class="ui massive primary button" type="submit">Uložit</button>
                        </div>
                     </div>
                </form>
            </div>

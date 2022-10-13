<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

use Site\ConfigurationCache;
use Red\Model\Entity\LoginAggregateFullInterface;

use Events\Middleware\Events\Controler\VisitorControler;
use Events\Model\Entity\VisitorProfile;
use Events\Model\Entity\Document;

/** @var PhpTemplateRendererInterface $this */
/** @var VisitorProfile $visitorData */
/** @var LoginAggregateFullInterface $loginAggregate */
/** @var Document $visitorDocumentCv */
/** @var Document $visitorDocumentLetter */


$userHash = $loginAggregate->getLoginNameHash();
$accept = implode(", ", ConfigurationCache::filesUploadController()['upload.events.acceptedextensions']);
$uploadedCvFilename = VisitorControler::UPLOADED_KEY_CV.$userHash;
$uploadedLetterFilename = VisitorControler::UPLOADED_KEY_LETTER.$userHash;

// formulář
// - pokud existuje registrace (loginAggregate má registration) defaultně nastaví jako email hodnotu z registrace $registration->getEmail(), pak input pro email je readonly
// - předvyplňuje se z $visitorData
$email = isset($visitorData) ? $visitorData->getEmail() : ($loginAggregate->getRegistration() ? $loginAggregate->getRegistration()->getEmail() : '');
?>
            <div class="active title">
                <i class="dropdown icon"></i>
                Balíček pracovních údajů
            </div>
            <div class="active content">
                <form class="ui huge form" action="events/v1/visitor" method="POST">
                    <div class="four fields">
                        <div class="three wide field">
                            <label>Titul před jménem</label>
                            <input type="text" name="prefix" placeholder="" maxlength="45" value="<?= isset($visitorData) ? $visitorData->getPrefix() : ''; ?>">
                        </div>
                        <div class="five wide field">
                            <label>Jméno</label>
                            <input type="text" name="name" placeholder="Jméno" maxlength="90" value="<?= isset($visitorData) ? $visitorData->getName() : ''; ?>">
                        </div>
                        <div class="five wide field">
                            <label>Příjmení</label>
                            <input type="text" name="surname" placeholder="Příjmení" maxlength="90" value="<?= isset($visitorData) ? $visitorData->getSurname() : ''; ?>">
                        </div>
                        <div class="three wide field">
                            <label>Titul za jménem</label>
                            <input type="text" name="postfix" placeholder="" maxlength="45" value="<?= isset($visitorData) ? $visitorData->getPostfix() : ''; ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>E-mail</label>
                            <input <?= $email ? "readonly" : '' ?> type="email" name="email" placeholder="mail@example.cz" maxlength="90" value="<?= $email ?>">
                        </div>
                        <div class="field">
                            <label>Telefon</label>
                            <input type="tel" name="phone" placeholder="+420 777 888 555" pattern="(\+420)\s[1-9]\d{2}\s\d{3}\s\d{3}" maxlength="45" value="<?= isset($visitorData) ? $visitorData->getPhone() : ''; ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Vzdělání, kurzy</label>
                            <textarea name="cv-education-text" class="working-data"><?= isset($visitorData) ? $visitorData->getCvEducationText() : ''; ?></textarea>
                        </div>
                        <div class="field margin">
                            <label>Pracovní zkušenosti, dovednosti</label>
                            <textarea name="cv-skills-text" class="working-data"><?= isset($visitorData) ? $visitorData->getCvSkillsText() : ''; ?></textarea>
                        </div>
                    </div>
                    <div class="field margin">
                        <button class="ui primary button" type="submit">Uložit údaje</button>
                    </div>
                     
                </form>
            </div>

                    <label><b>Nahrané soubory</b></label>
                    <div class="fields">
                        <div class="field">
                            <form class="ui huge form" action="events/v1/removevisitorfilecv" method="POST" >
                                <p>Životopis: <?= isset($visitorDocumentCv) ? $visitorDocumentCv->getDocumentFilename() : ''; ?>
                                    <input  type="text"  name="cvId"  value= <?= isset($visitorDocumentCv) ? $visitorDocumentCv->getId() : '' ; ?> >
                                </p>
                                 <button type="submit" formaction="events/v1/removevisitorfile" >Odstranit životopis</button> 
                            </form>
                            
                            
                            <p>Motivační dopis: <?= isset($visitorDocumentLetter) ? $visitorDocumentLetter->getDocumentFilename() : ''; ?></p>
                        </div>
                        
                        
                        
                        
<!--                        <div class="field">
                                <span class="text maly okraje-horizontal"><a><i class="eye outline icon"></i>Zobrazit soubor</a></span>
                                <span class="text maly okraje-horizontal"><a><i class="trash icon"></i>Smazat</a></span>
                        </div>-->

                    </div>  
                    
                    
                <!--odesílá k uložení do souboru-->
                <form class="ui huge form" action="events/v1/uploadvisitorfile" method="POST" enctype="multipart/form-data">
                     <div class="two fields">
                        <div class="field margin">
                            <label><?= (isset($visitorDocumentCv) AND $visitorDocumentCv->getDocumentFilename()) ? 'Příloha - můžete nahrát jiný životopis' : 'Příloha - životopis'; ?></label>
                            <input type="file" name="<?= $uploadedCvFilename ?>" accept="<?= $accept ?>"  "multiple"=0 size="1">
                        </div>
                        <div class="field margin">
                            <button class="ui primary button" type="submit">Uložit životopis</button>
                        </div>
                     </div>
                </form>
                <form class="ui huge form" action="events/v1/uploadvisitorfile" method="POST" enctype="multipart/form-data">
                     <div class="two fields">
                        <div class="field margin">
                            <label><?= (isset($visitorDocumentLetter) AND $visitorDocumentLetter->getDocumentFilename()) ? 'Příloha - můžete nahrát jiný motivační dopis' : 'Příloha - motivační dopis'; ?></label>
                            <input type="file" name="<?= $uploadedLetterFilename ?>" accept="<?= $accept ?>"  "multiple"=0 size="1">
                        </div>
                        <div class="field margin">
                            <button class="ui primary button" type="submit">Uložit dopis</button>
                        </div>
                     </div>
                </form>
            

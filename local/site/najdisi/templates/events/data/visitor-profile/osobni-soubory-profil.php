<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

use Site\ConfigurationCache;

/** @var PhpTemplateRendererInterface $this */
 if ($editable) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }   


?>
          

                <label><b>Nahrané soubory</b></label>                    
                <form class="ui huge form"  method="POST" >
                    <div class="two fields">
                        <div class="field">
                            <p>Životopis:<b> <?= isset($visitorDocumentCv) ? $visitorDocumentCv->getDocumentFilename() : ''; ?></b></p>                                                        
                             <?= isset($visitorDocumentCv) ?
                                '<button type="submit" formaction="events/v1/document/' .$visitorDocumentCv->getId(). '/remove" >Odstranit životopis</button>'
                                : '' ;   ?>
                        </div>      
                        <div class="field">
                            <p>Motivační dopis:<b> <?= isset($visitorDocumentLetter) ? $visitorDocumentLetter->getDocumentFilename() : ''; ?></b></p>                            
                            <?= isset($visitorDocumentLetter) ?
                                "<button type='submit' formaction='events/v1/document/" .$visitorDocumentLetter->getId(). "/remove' >Odstranit motivační dopis</button>"
                                : "" ;   ?>
                        </div>
                    </div>
                </form>                  
                <br/>
<!--    <div class="field">
                                <span class="text maly okraje-horizontal"><a><i class="eye outline icon"></i>Zobrazit soubor</a></span>
                                <span class="text maly okraje-horizontal"><a><i class="trash icon"></i>Smazat</a></span>
        </div>-->                   
                                  
                <!--odesílá k uložení do souboru-->
                <form class="ui huge form" action="events/v1/uploadvisitorfile" method="POST" enctype="multipart/form-data">
                     <div class="two fields">
                        <div class="field margin">
                            <label><?= (isset($visitorDocumentCv) AND $visitorDocumentCv->getDocumentFilename()) ? 'Příloha - můžete nahrát jiný životopis' : 'Příloha - životopis'; ?></label>
                            <input type="file" name="<?= $uploadedCvFilename ?>" accept="<?= $accept ?>"  "multiple"=0 size="10000000">
                            <p>Akceptované typy souborů: <?= $accept ?> Max. velikost souboru: 10 MB.</p>
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
                            <input type="file" name="<?= $uploadedLetterFilename ?>" accept="<?= $accept ?>"  "multiple"=0 size="10000000">
                            <p>Akceptované typy souborů: <?= $accept ?> Max. velikost souboru: 10 MB.</p>
                        </div>
                        <div class="field margin">
                            <button class="ui primary button" type="submit">Uložit dopis</button>
                        </div>
                     </div>
                </form>
             
            </div>
                
            

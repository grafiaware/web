<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */  
use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;
    
?>

        <div class="two fields">   
        <div class="field">
            <label><b>Nahraný soubor</b></label>                    
            <p><b> <?= $filename ?? '---' ?></b></p>                                                                 
        </div>              
            <div class="field margin">
                <label>
                    <?= ( isset($filename) ) ?  ' - můžete nahrát jiný ' : ''; ?></label>
                <input type="file" name="<?= $uploadedFilename ?>" accept="<?= $accept ?>"  "multiple"=0 size="10000000">
                <p>Akceptované typy souborů: <?= $accept ?> Max. velikost souboru: 10 MB.</p>
            </div>
        </div>                
                                 
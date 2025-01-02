<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */  
use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;

if ($editable) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }        
?>
    <?= ($addHeadline ?? TRUE) ? "<p>$addHeadline</p>" : "" ?>

    <label><b>Nahraný soubor</b></label>                    
    <form class="ui huge form"  method="POST" >
        <div class="field">
            <p><b> <?= $filename ?? '---' ?></b></p>                                                        
            <?=
            ( ($editable ?? false) && ($id ?? false) ) ?
                        "<button class='ui primary button' type='submit' formaction='$actionRemove' > Odstranit </button>" : "";
            ?>
        </div>                              
    </form>                  
     
    <form class="ui huge form" method="POST" enctype="multipart/form-data">
        <div class="two fields">   
            <div class="field margin">
                <label>
                    <?= ( isset($filename) ) ? 
                        ' - můžete nahrát jiný ' : ''; ?></label>
                <input type="file" name="<?= $uploadedFilename ?>" accept="<?= $accept ?>"  "multiple"=0 size="10000000">
                <p>Akceptované typy souborů: <?= $accept ?> Max. velikost souboru: 10 MB.</p>
            </div>
        </div>                
        <!--buttons-->
        <div>
            <?=
            ($editable ?? false) ? 
                (isset($id)  ?
                    "<button class='ui primary button' type='submit' formaction='$sctionSave' > Uložit změny </button>"
                :
                    "<button class='ui primary button' type='submit' formaction='$actionAdd' > Přidat </button>" 
                )        
            : "";
            ?>

        </div>   
    </form>                                     
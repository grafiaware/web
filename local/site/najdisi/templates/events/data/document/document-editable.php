<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
if ($editable) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }        
?>
    <label><b>Nahraný soubor</b></label>                    
    <form class="ui huge form"  method="POST" >
        <div class="field">
            <p>------<b> <?= $filename ?? '' ?></b></p>                                                        
            <?=
            ($editable ?? false) && ($remove ?? false) ? "<button class='ui primary button' type='submit' formaction='$componentRouteSegment/$id/remove' > Odstranit </button>" : "";
            ?>
        </div>                              
    </form>                  

        <?= $addHeadline ?? false ? "<p>$addHeadline</p>" : "" ?>

    <form class="ui huge form" method="POST" enctype="multipart/form-data">
        <div class="two fields">   
            <div class="field margin">
                <label><?= ( isset($filename) ) ? 
                  ' - můžete nahrát jiný ' : '- životopis'; ?></label>
                <input type="file" name="<?= $uploadedFilename ?>" accept="<?= $accept ?>"  "multiple"=0 size="10000000">
                <p>Akceptované typy souborů: <?= $accept ?> Max. velikost souboru: 10 MB.</p>
            </div>
        </div>                
        <!--buttons-->
        <div>
            <?=
            $editable ?? false ? 
                (isset($id) 
                ?
                    "<button class='ui primary button' type='submit' formaction='$componentRouteSegment/$id' > Uložit změny </button>"
                :
                    "<button class='ui primary button' type='submit' formaction='$componentRouteSegment' > Přidat </button>" 
                )        
            : "";
            ?>

        </div>   
    </form>                                     
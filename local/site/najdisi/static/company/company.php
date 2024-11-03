<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

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

        <form class="ui huge form" action="" method="POST" >

            <div class="two fields">                        
                <div class="field">
                <label>Název firmy</label>
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="250" value="<?= $name ?? '' ?>" required >
                 </div>  
            </div>                

                <?php
                if($editable) {
                ?>
                <div>
                    <?=
                     isset($companyId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId . "' > Uložit změny </button>" :
                    "<button class='ui primary button' type='submit' formaction='events/v1/company' > Uložit </button>" ;                                   
                     ?>                                                                                                                             
                    <?=
                    isset($companyId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId."/remove' > Odstranit firmu </button>" :
                    "" ;
                    ?>                                                                                                         
                </div>
                <?php
                }
                ?>

    
        </form>           
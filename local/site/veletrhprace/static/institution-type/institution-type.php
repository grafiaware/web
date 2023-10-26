<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

    //    $readonly = 'readonly="1"';
    //    $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = ''; 
?>

        <form class="ui huge form" action="" method="POST" >                                                                         
                <div class="field">                   
                    <label>Typ instituce</label>
                    <input <?= $readonly ?> type="text" name="institutionType" required placeholder="" maxlength="45" 
                                            value="<?= isset($institutionType)?  $institutionType : '' ?>">                   
                </div>           
            
     
            <?php
            if($readonly === '') {
            ?>
            <div>
                <?=
                 isset($institutionTypeId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/institutiontype/".$institutionTypeId. "' > Uložit </button>" :
                "<button class='ui primary button' type='submit' formaction='events/v1/institutiontype' > Uložit </button>" ;                                   
                 ?>   

                <?=
                isset($institutionTypeId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/institutiontype/".$institutionTypeId."/remove' > Odstranit typ instituce </button>" :
                "" ;
                ?>                                                                                                         
            </div>
            <?php
            }
            ?>

        </form>           
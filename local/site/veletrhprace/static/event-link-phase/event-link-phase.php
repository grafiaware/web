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
                    <label>Fáze odkazu</label>
                    <input <?= $readonly ?> type="text" name="eventLinkPhaseText" placeholder="" maxlength="100" required
                                            value="<?= isset($eventLinkPhaseText)?  $eventLinkPhaseText : '' ?>">                   
                </div>           
            
     
            <?php
            if($readonly === '') {
            ?>
            <div>
                <?=
                 isset($eventLinkPhaseId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/eventlinkphase/".$eventLinkPhaseId. "' > Uložit </button>" :
                "<button class='ui primary button' type='submit' formaction='events/v1/eventlinkphase' > Uložit </button>" ;                                   
                 ?>   

                <?=
                isset($eventLinkPhaseId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/eventlinkphase/".$eventLinkPhaseId."/remove' > Odstranit fázi </button>" :
                "" ;
                ?>                                                                                                         
            </div>
            <?php
            }
            ?>

        </form>           
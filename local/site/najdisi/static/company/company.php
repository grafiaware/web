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

            <div class="two fields">                        
                <div class="field">
                <label>Název firmy</label>
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="250" value="<?= isset($name)?  $name : '' ?>">
                 </div>  
<!--                <div class="field">
                    <label>E-eventInstitutionName30</label>
                    <input $readonly  type="text" name="eventInstitutionName30" placeholder="" maxlength="30" 
                                            value=" isset($eventInstitutionName30)?  $eventInstitutionName30 : ''   ">
                </div>-->
            </div>                

                <?php
                if($readonly === '') {
                ?>
                <div>
                    <?=
                     isset($companyId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId . "' > Uložit </button>" :
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
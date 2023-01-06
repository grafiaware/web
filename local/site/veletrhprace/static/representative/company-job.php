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
            <input type='hidden' name="company-id" value="<?= isset($companyId)? $companyId : '' ?>" >
            <input type='hidden' name="company-job-id" value="<?= isset($companyJobId)? $companyJobId : '' ?>" >

            <div class="two fields">                        
                <div class="field">
                <label>Název jobu</label>
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="45" value="<?= isset($name)?  $name : '' ?>">
                 </div>                 
                
                <div class="field">
                    <label>Požadované vzdělání</label>
                    <input <?= $readonly ?> type="text" name="pozadovane-vzdelani-stupen" placeholder="" maxlength="100" 
                                            value="<?= isset($pozadovaneVzdelaniStupen)?  $pozadovaneVzdelaniStupen : ''  ?>">
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>Místo výkonu</label>
                    <input <?= $readonly ?> type="text" name="misto-vykonu" placeholder="" maxlength="45" 
                                            value="<?= isset($mistoVykonu)?  $mistoVykonu : '' ?>">
                </div>
                <div class="field">
                    <label>Popis pozice</label>
                    <input <?= $readonly ?> type="text" name="popis-pozice" placeholder="" maxlength="1000" 
                                            value="<?= isset($popisPozice)?  $popisPozice : '' ?>">
                </div>
            </div>                 

                <?php
                if($readonly === '') {
                ?>
                <div>
                    <?=
                     isset($companyContactId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/companycontact/". $companyContactId ."' > Uložit </button>" :
                    "<button class='ui primary button' type='submit' formaction='events/v1/companycontact' > Uložit </button>" ;
                    ?>                                                                                                                             
                    <?=
                     isset($companyContactId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/companycontact/". $companyContactId ."/remove' > Odstranit kontakt </button>" :
                    "" ;
                    ?>                                                                                                         
                </div>
                <?php
                }
                ?>

        </form>           
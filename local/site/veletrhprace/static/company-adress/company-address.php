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

            <div class="two fields">                        
                <div class="field">
                <label>Jméno adresy</label>
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="100" value="<?= isset($name)?  $name : '' ?>">
                 </div>  
                <div class="field">
                    <label>Lokace</label>
                    <input <?= $readonly ?> type="text" name="lokace" placeholder="" maxlength="100" value="<?= isset($lokace)?  $lokace : ''  ?>">
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>PSČ</label>
                    <input <?= $readonly ?> type="text" name="psc" placeholder="" maxlength="5" value="<?= isset($psc)?  $psc : '' ?>">
                </div>
                <div class="field">
                    <label>Obec</label>
                    <input <?= $readonly ?> type="text" name="obec" placeholder="" maxlength="60" value="<?= isset($obec)?  $obec : '' ?>">
                </div>
            </div>                 

                <?php
                if($readonly === '') {
                ?>
                <div>
                    <?=
                     isset($name) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/companyaddress/".  $companyId ."' > Uložit </button>" :
                    "<button class='ui primary button' type='submit' formaction='events/v1/companyaddress' > Uložit </button>" ;
                    ?>                                                                                                                             
                    <?=
                     isset($name) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/companyaddress/". $companyId ."/remove' > Odstranit adresu </button>" :
                    "" ;
                    ?>                                                                                                         
                </div>
                <?php
                }
                ?>

        </form>           
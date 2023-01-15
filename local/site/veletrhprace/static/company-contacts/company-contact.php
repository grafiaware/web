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
                <label>Jméno kontaktu</label>
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="100" value="<?= isset($name)?  $name : '' ?>">
                 </div>  
                <div class="field">
                    <label>E-maily</label>
                    <input <?= $readonly ?> type="email" name="emails" placeholder="" maxlength="100" value="<?= isset($emails)?  $emails : ''  ?>">
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>Telefony</label>
                    <input <?= $readonly ?> type="text" name="phones" placeholder="" maxlength="60" value="<?= isset($phones)?  $phones : '' ?>">
                </div>
                <div class="field">
                    <label>Mobily</label>
                    <input <?= $readonly ?> type="text" name="mobiles" placeholder="" maxlength="60" value="<?= isset($mobiles)?  $mobiles : '' ?>">
                </div>
            </div>                 

                <?php
                if($readonly === '') {
                ?>
                <div>
                    <?=
                     isset($companyContactId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId."/companycontact/". $companyContactId ."' > Uložit </button>" :
                    "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId."/companycontact' > Uložit </button>" ;              
                     
                     ?>                                                                                                                             
                    <?=
                    isset($companyContactId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId."/companycontact/". $companyContactId ."/remove' > Odstranit kontakt </button>" :
                    "" ;
                    ?>                                                                                                         
                </div>
                <?php
                }
                ?>

        </form>           
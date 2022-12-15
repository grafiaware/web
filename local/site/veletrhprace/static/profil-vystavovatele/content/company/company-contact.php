<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

    //    $readonly = 'readonly="1"';
    //    $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = '';
?>
                 
                <form class="ui huge form" action="" method="POST" >
                    <input type='hidden' name="company-id" value="<?= isset($companyId)? $companyId : '' ?>" >
                    <input type='hidden' name="company-contact-id" value="<?= isset($companyContactId)? $companyContactId : '' ?>" >
                    
                    <div class="two fields">                        
                        <div class="field">
                        <label>Jméno kontaktu</label>
                            <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="90" value="<?= isset($name)?  $name : '' ?>">
                         </div>  
                        <div class="field">
                            <label>E-maily</label>
                            <input <?= $readonly ?> type="email" name="emails" placeholder="" maxlength="90" value="<?= isset($emails)?  $emails : ''  ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Telefony</label>
                            <input <?= $readonly ?> type="text" name="phones" placeholder="" maxlength="90" value="<?= isset($phones)?  $phones : '' ?>">
                        </div>
                        <div class="field">
                            <label>Mobily</label>
                            <input <?= $readonly ?> type="text" name="mobiles" placeholder="" maxlength="90" value="<?= isset($mobiles)?  $mobiles : '' ?>">
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
                      <!--  </div>
                        <div> -->
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



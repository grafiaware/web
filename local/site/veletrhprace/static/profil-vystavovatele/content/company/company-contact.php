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
                 
                <form class="ui huge form" action="events/v1/companycontact" method="POST" >
                    <input type='hidden' name="company-id" value="<?= $companyId ?>" >
                    
                    <div class="two fields">                        
                        <div class="field">
                        <label>Jméno kontaktu</label>
                            <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="90" value="<?= $name ?>">
                         </div>  
                        <div class="field">
                            <label>E-maily</label>
                            <input <?= $readonly ?> type="email" name="emails" placeholder="" maxlength="90" value="<?= $emails ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Telefony</label>
                            <input <?= $readonly ?> type="text" name="phones" placeholder="" maxlength="90" value="<?= $phones ?>">
                        </div>
                        <div class="field">
                            <label>Mobily</label>
                            <input <?= $readonly ?> type="text" name="mobiles" placeholder="" maxlength="90" value="<?= $mobiles ?>">
                        </div>
                    </div>                 
                    <div>
                        <?php
                        if($readonly === '') {
                        ?>
                        <div>
                            <?=
                             isset($companyId) ?
                            '<button class="ui primary button" type="submit" formaction="events/v1/companycontact/". $companyId > Uložit </button>' :
                            '<button class="ui primary button" type="submit" formaction="events/v1/companycontact"> Uložit </button>' 
                            ?>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </form>           



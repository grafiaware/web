<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
?>
        <form class="ui huge form" action="" method="POST" >

            <div class="two fields">                        
                <div class="field">
                <label>Jméno kontaktu</label>
                    <input readonly type="text" name="name" placeholder="" maxlength="100" value="<?= $name ?? '' ?>" required>
                 </div>  
                <div class="field">
                    <label>E-maily</label>
                    <input readonly type="email" name="emails" placeholder="" maxlength="100" value="<?= $emails ?? ''  ?>">
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>Telefony</label>
                    <input readonly type="text" name="phones" placeholder="" maxlength="60" value="<?= $phones ?? '' ?>">
                </div>
                <div class="field">
                    <label>Mobily</label>
                    <input readonly type="text" name="mobiles" placeholder="" maxlength="60" value="<?= $mobiles ?? '' ?>">
                </div>
            </div>                 
        </form>           
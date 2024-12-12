<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
?>

            <div class="two fields">                        
                <div class="field">
                    <label>Jméno kontaktu</label>
                    <p><?= $name ?? '' ?></p>
                 </div>  
                <div class="field">
                    <label>E-maily</label>
                    <p><?= $emails ?? ''  ?></p>
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>Telefony</label>
                    <p><?= $phones ?? '' ?></p>
                </div>
                <div class="field">
                    <label>Mobily</label>
                    <p><?= $mobiles ?? '' ?></p>
                </div>
            </div>                 

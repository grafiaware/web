<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

?> 
            <div class="two fields">                        
                <div class="field">
                <label>Jméno kontaktu</label>
                    <input  type="text" name="name" placeholder="" maxlength="100" value="<?= $name ?? '' ?>">
                    <span></span>
                 </div>  
                <div class="field">
                    <label>E-mail</label>
                    <input  type="email" name="emails" placeholder="" maxlength="100" value="<?= $emails ?? ''  ?>">
                    <span></span>
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>Telefon</label>
                    <input  type="tel" pattern="[+]?[0-9 ]{6,17}" name="phones" placeholder="" maxlength="60" value="<?= $phones ?? '' ?>">
                    <span></span>
                </div>
                <div class="field">
                    <label>Mobil</label>
                    <input  type="tel" pattern="[+]?[0-9 ]{6,17}" name="mobiles" placeholder="" maxlength="60" value="<?= $mobiles ?? '' ?>">
                    <span></span>
                </div>
            </div>     
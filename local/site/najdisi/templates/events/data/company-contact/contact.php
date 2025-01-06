<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
?>
            <div class="ui grid">
                <div class="eight wide column">
                    <p class="text primarni-barva zadne-okraje"><?= $name ?? '' ?></p>
                    <p class="text primarni-barva zadne-okraje"><i class="id badge outline icon"></i>Kontaktujte nás</p>
                 </div>  
                <div class="eight wide column">
                    <?php if($mobiles){ ?>
                        <p class="text zadne-okraje"><a href="mailto:<?= $emails ?? '' ?>"><i class="mail icon"></i> <?= $emails ?? '' ?> </a></p> 
                    <?php }?>
                    <p class="text zadne-okraje">
                        <?php if($mobiles){ ?>
                        <a href="tel:<?= $mobiles ?>"><i class="mobile alternate icon"></i> <?= $mobiles ?></a> 
                        <?php }?>
                        <?php if($phones){ ?>
                        <a href="tel:<?= $phones ?>"><i class="phone icon"></i> <?= $phones ?></a>
                        <?php }?>
                    </p> 
                </div>
            </div>

<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
?>

                <div class="eight wide column">
                    <p class="text primarni-barva zadne-okraje"><?= $name ?? '' ?></p>
                    <p class="text primarni-barva zadne-okraje"><i class="id badge outline icon"></i>Kontaktujte nás</p>
                 </div>  
                <div class="eight wide column">
                    <p class="text zadne-okraje"><i class="mail icon"></i> <?= $emails ?? '' ?> </p> 
                    <?php if($mobiles || $phones){ ?>
                        <p class="text zadne-okraje"><i class="mobile alternate icon"></i><i class="phone icon"></i><?= $mobiles ?> <?= $phones ?></p> 
                    <?php }?>
                </div>

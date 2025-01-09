<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

?>
           <div class="eight wide column">
                <div class="ui segment">
                    <p class="text primarni-barva zadne-okraje"><i class="id badge outline icon"></i><?= $name ?? '' ?></p>
                    <?php if($mobiles){ ?>
                    <p class="text zadne-okraje"><a href="tel:<?= $mobiles ?>"><i class="mobile alternate icon"></i> <?= $mobiles ?></a> </p> 
                    <?php }?>
                    <?php if($phones){ ?>
                    <p class="text zadne-okraje"><a href="tel:<?= $phones ?>"><i class="phone icon"></i> <?= $phones ?></a></p> 
                    <?php }?>
                    <?php if($emails){ ?>
                        <p class="text zadne-okraje"><a href="mailto:<?= $emails ?? '' ?>"><i class="mail icon"></i> <?= $emails ?? '' ?> </a></p> 
                    <?php }?>
                </div>
            </div>

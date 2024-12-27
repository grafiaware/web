<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>


            <div class="">
                <?= $listHeadline ?>
            </div>     
            <div class="list active content">      
                <?= $this->insert( __DIR__.'/items.php', $items, __DIR__.'/empty.php') ?>                           
            </div>            
    </div>

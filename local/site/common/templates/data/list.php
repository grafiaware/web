<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>

    <div class="ui styled fluid accordion">   

            <div class="active title">
                <i class="dropdown icon"></i>
                <?= $listHeadline ?>
            </div>    
            <div class="active content">
                <?php $listUid = uniqid(); ?>
                <div id="list_<?php $listUid;?>" class="list active content">      
                    <?= $this->insert( __DIR__.'/items.php', $items+["listUid"=>$listUid], __DIR__.'/empty.php') ?>                           
                </div> 
            </div>
    </div>

<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>


            <div class="ui grid stackable">     
                <div class="sixteen wide column">
                    <p class="podnadpis"> <?= $listHeadline ?></p>
                </div>
                <div class="sixteen wide column">
                    <div class="ui grid stackable">
                        <?= $this->insert( __DIR__.'/items.php', $items, __DIR__.'/empty.php') ?>    
                    </div>
                </div>
            </div>            

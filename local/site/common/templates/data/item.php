<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>

        <!--<div>-->
            <!--<div class="two fields">-->   
                <?= $this->insert($fieldsTemplate, $fields  ?? []) ?>
            <!--</div>-->                
        <!--</div>-->         
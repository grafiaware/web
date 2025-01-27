<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>
 
        <?= $this->insert($fieldsTemplate, $fields  ?? []) ?>       
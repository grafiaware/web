<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>
 
        <?= $this->insert($fieldsTemplate, $fields  ?? []) ?>       
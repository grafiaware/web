<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>
        <?= $infoText ?? false ? "<p class='ui blue segment'>$infoText</p>" : "" ?>

        <?= $this->insert($fieldsTemplate, $fields  ?? []) ?>     
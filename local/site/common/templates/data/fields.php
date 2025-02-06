<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>
        <?= $fieldsInfoText ?? false ? "<p class='ui blue segment'>$fieldsInfoText</p>" : "" ?>

        <?= $this->insert($fieldsTemplate, $fields ?? [], __DIR__.'/noData.php') ?>     


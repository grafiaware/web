<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>
        <?= $fieldsInfoText ?? false ? "<p class='ui blue segment'>$fieldsInfoText</p>" : "" ?>

        <?= $this->insert($fieldsTemplate, $fields ?? [], __DIR__.'/noData.php') ?>     


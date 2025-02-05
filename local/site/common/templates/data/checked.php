<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>
    <p class="text tucne zadne-okraje"><?= $listHeadline ?></p>
            

<?= 
$this->repeat(__DIR__.'/wrapper.php', $items, 'item');
?>
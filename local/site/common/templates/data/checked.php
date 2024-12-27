<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>
<?= 
$this->repeat(__DIR__.'/wrapper.php', $items, 'item');
?>
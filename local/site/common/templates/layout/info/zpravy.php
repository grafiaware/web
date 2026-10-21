<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;
/** @var PhpTemplateRendererInterface $this */

?>

<div class="pro-media">
    <div class="ui grid">
        <?= $this->repeat(__DIR__.'/zpravy/zprava.php', $context) ?>
    </div>
</div>

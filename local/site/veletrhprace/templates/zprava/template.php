<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */

?>

<div class="pro-media">
    <div class="ui grid">
        <?= $this->repeat(__DIR__.'/content/zprava.php', $context) ?>
    </div>
</div>

<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

?>

<div class="pro-media">
    <div class="ui grid">
        <?= $this->repeat(__DIR__.'/tiskova-zprava/zprava.php', $tiskovaZprava) ?>
    </div>
</div>

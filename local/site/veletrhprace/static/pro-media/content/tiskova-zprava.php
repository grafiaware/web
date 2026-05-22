<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */

?>

<div class="pro-media">
    <div class="ui grid">
        <?= $this->repeat(__DIR__.'/tiskova-zprava/zprava.php', $tiskovaZprava) ?>
    </div>
</div>

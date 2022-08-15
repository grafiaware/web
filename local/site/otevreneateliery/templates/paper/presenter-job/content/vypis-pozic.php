<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>
<div class="vypis-prac-pozic">
    <div class="ui styled fluid accordion">

        <?= $this->repeat(__DIR__.'/vypis-pozic/pozice.php', $jobs)?>
    </div>
</div>



<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */
?>

<div class="paper">
    <headline>
        <?php include "headline.php" ?>
    </headline>
    <perex>
        <?php include "perex.php" ?>
    </perex>
    <content>
        <?php include "content/logo.php" ?>
    </content>
</div>

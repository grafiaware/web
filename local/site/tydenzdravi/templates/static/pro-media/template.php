<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<div class="paper">
    <headline>
         <?php include "pro-media/headline.php" ?>
    </headline>
    <perex>
        <?php include "pro-media/perex.php" ?>
    </perex>
    <content>
        <?php include "pro-media/content/content_ProMedia.php" ?> 
    </content>
</div>

<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<div class="paper">
    <headline>
        <?php include "kupony-s-vyhodou/headline.php" ?>
    </headline>
    <perex>
        <?php include "kupony-s-vyhodou/perex.php" ?>
    </perex>
    <content>
        <?php include "kupony-s-vyhodou/content/content_KuponySVyhodou.php" ?>
    </content>
</div>


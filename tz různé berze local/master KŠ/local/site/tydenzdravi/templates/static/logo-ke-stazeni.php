<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<div class="paper">
    <headline>
        <?php include "logo-ke-stazeni/headline.php" ?>
    </headline>
    <perex>
        <?php include "logo-ke-stazeni/perex.php" ?>
    </perex>
    <content>
        <?php include "logo-ke-stazeni/content/content_LogoKeStazeni.php" ?>
    </content>
</div>

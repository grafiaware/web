<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>
<div class="prednasky">
    <headline>
        <?php include "body-pro-zdravi/headline.php" ?>
    </headline>
    <perex>
        <?php include "body-pro-zdravi/perex.php" ?>
    </perex>
    <content>
        <?php include "body-pro-zdravi/content/content_BodyZdravi.php" ?>
    </content>
    <content>
        <div class="velky text do-kraju">
            <p>
                <?= $this->mono('Další body pro vaše zdraví budou postupně přibývat, sledujte tuto stránku!')?>
            </p>
        </div>
    </content>
</div>
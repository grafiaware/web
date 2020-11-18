<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>
<div class="paper">
    <headline>
        <?php include "prednasky/headline.php" ?>
    </headline>
    <perex>
        <?php include "prednasky/perex.php" ?>
    </perex>
    <content>
        <?php include "prednasky/content/content_Prednasky.php" ?>
    </content>
    <content>
        <div class="velky text okraje-vertical">
            <p>
                <?= $this->mono('Další jména přednášejících budou postupně přibývat, sledujte tuto stránku!')?>
            </p>
        </div>
    </content>
</div>
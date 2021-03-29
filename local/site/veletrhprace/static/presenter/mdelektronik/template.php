<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */


include 'data.php';
?>

<article class="paper">
    <section>
        <headline>
            <?= $this->insert(__DIR__.'/headline.php', $firma)?>
        </headline>
        <perex>
            <?php include "perex.php" ?>
        </perex>
    </section>
    <section>
        <?= $this->insert(__DIR__.'/content/stanek.php', $firma)?>
    </section>
</article>
<?php // include "pracovni-pozice/template.php" ?>
<?php // include "nas-program/template.php" ?>
<?php //include "chci-na-online-pohovor/template.php" ?>
<?php // include "chci-navazat-kontakt/template.php" ?> 

<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */

include 'data.php';
include __DIR__.'/../presenterdata.php';

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
    <?php include "pracovni-pozice/template.php" ?>
    <?php include "program/template.php" ?>
    <?php include "chatujte-s-nami-(Eures)/template.php" ?>
    <?php include "kontakty/template.php" ?>
</article>

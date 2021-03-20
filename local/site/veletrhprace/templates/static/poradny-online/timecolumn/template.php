<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */


?>
<div class="paper">
    <headline>
        <?php include "headline.php" ?>
    </headline>
    <perex>
        <?php include "perex.php" ?>
    </perex>
    <content>
         <?= include'content/timecolumn.php' ?>

    </content>
</div>
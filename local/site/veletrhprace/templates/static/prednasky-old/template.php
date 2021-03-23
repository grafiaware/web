<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

include 'data.php';
?>
<div class="paper">
    <headline>
        <?php include "headline.php" ?>
    </headline>
    <perex>
        <?php include "perex.php" ?>
    </perex>
    <content>
         <?= $this->repeat(__DIR__.'/content/timecolumn.php', $event) ?>
    </content>
    <?php include "content/footer.php" ?>
</div>
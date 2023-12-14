<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */


?>
<div class="paper">
    <headline>
        <?php include "timecolumn/headline.php" ?>
    </headline>
    <perex>
        <?php include "timecolumn/perex.php" ?>
    </perex>
    <content>
        <?php include'timecolumn/content/timecolumn.php' ?>
    </content>
    <content>
        <?php include "timecolumn/footer.php" ?>
    </content>
</div>
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
        <?php include'content/timecolumn.php' ?>
    </content>
    <content>
        <?php include "footer.php" ?>
    </content>
</div>
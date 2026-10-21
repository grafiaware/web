<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */
?>

<p>
    <?=
            $active ?
                "<a href=\"web/v1/page/item/$uid#$anchor\">$nazev</a>"
            :
                "$nazev"
    ?>
</p>

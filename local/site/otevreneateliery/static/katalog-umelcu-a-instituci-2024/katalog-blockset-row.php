<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

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

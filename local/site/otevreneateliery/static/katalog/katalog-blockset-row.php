<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
?>

<div>     
    <?=         
            $active ?
                "<a href=\"web/v1/page/item/$uid#$anchor\">$nazev</a>"
            :
                "$nazev"
    ?> 
 </div> 


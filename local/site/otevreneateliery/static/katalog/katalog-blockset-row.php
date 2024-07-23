<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
?>

<div>     
    <?=         
            $webAnchor ?
                "<a href=\"web/v1/page/item/$site#$webAnchor\">$prijmeni $jmeno</a>"
            :
                "$prijmeni $jmeno "
    ?> 
 </div> 


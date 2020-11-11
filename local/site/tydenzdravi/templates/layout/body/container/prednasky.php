<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>
<div class="prednasky">
    <headline>  
        <?php include "headline/headlinePrednasky.php" ?> 
    </headline>
    <perex>  
        <?php include "perex/perexPrednasky.php" ?> 
    </perex>
    <content> 
        <?php include "content/content_Prednasky.php" ?> 
    </content>
</div>
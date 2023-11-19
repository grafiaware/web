<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

echo Html::tag('div', 
        [
            'id'=>$loaderElementId,
            'class'=>$class,
            'data-red-apiuri'=>$dataRedApiUri,
            'data-red-cache-control'=>$dataRedCacheControl
        ]
    )

?>


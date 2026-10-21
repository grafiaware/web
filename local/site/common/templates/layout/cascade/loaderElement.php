<?php
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

echo Html::tag('div', 
        [
            'id'=>$id,
            'class'=>$class,
            'data-red-apiuri'=>$dataRedApiUri,
//            'data-red-cache-control'=>$dataRedCacheControl,
//            'data-nav-target-id'=>$dataNavTargetId
        ]
    );


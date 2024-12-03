<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

$pStyle = ['style'=>'color: red;'];
echo Html::p("Všechny joby pro company s id 25: events/v1/subdata/companyJob/25", $pStyle);
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/subdata/companyJob/25",
        ]
    );

$pStyle = ['style'=>'color: red;'];
echo Html::p("Jeden job s id: events/v1/data/companyJob/8", $pStyle);
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/data/companyJob/8",
        ]
    );


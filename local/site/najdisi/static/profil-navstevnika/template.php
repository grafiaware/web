<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
$pStyle = ['style'=>'color: blue;'];

//echo Html::tag('div', 
//        [
//            'class'=>'cascade',
//            'data-red-apiuri'=>"events/v1/data/document/xx",
//        ]
//    );







echo Html::p("--------------- PROFILE ------------", $pStyle);

$loginName = "visitor";
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/data/visitorProfile/$loginName"
        ]
    );

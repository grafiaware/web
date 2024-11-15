<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
$pStyle = ['style'=>'color: red;'];
echo Html::p("events/v1/component/companyList", $pStyle);
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/component/companyList",
        ]
    );
echo Html::p("events/v1/component/company/10", $pStyle);
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/component/company/10",
        ]
    );
echo Html::p("events/v1/component/companyAddress/10", $pStyle);
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/subcomponent/companyAddress/10",
        ]
    );
echo Html::p("events/v1/component/companyContactList", $pStyle);
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/component/companyContactList",
        ]
    );
echo Html::p("events/v1/subcomponent/companyContactList/10", $pStyle);
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/subcomponent/companyContactList/10",
        ]
    );

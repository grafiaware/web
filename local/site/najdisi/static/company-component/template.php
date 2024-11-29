<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
$pStyle = ['style'=>'color: red;'];
echo Html::p("Všechny company: events/v1/component/company", $pStyle);
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/component/company",
        ]
    );
echo Html::p("Jedna company s id company: events/v1/component/company/10", $pStyle);
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/component/company/10",
        ]
    );
echo Html::p("Jedna adresa s id adresy: events/v1/component/companyAddress/10", $pStyle);
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/component/companyAddress/10",
        ]
    );
echo Html::p("Všechny kontakty jedné company s id company (rodiče): events/v1/subcomponent/companyContact/10", $pStyle);
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/subcomponent/companyContact/10",
        ]
    );

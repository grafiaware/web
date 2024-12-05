<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Events\Model\Repository\CompanyRepo;

    /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );
    $companies = $companyRepo->findAll();
    
    $pStyle = ['style'=>'color: red;'];

    echo Html::p("Všechny tagy: events/v1/data/tag", $pStyle);
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/tag",
            ]
        );

    echo Html::p("Jeden job s id: events/v1/data/companyJob/8", $pStyle);
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/companyJob/8",
            ]
        );
    
    echo Html::tag('h4', $pStyle, "Cyklus pro všechny company");

    foreach ($companies as $company) {
        $companyId = $company->getId();
        echo Html::tag('div',['style'=>'background-color: yellow; color: red;'] ,$company->getName());
        

        echo Html::p("Všechny joby pro company s id $companyId: events/v1/subdata/companyJob/$companyId", $pStyle);
        echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/subdata/companyJob/$companyId",
                ]
            );
    }

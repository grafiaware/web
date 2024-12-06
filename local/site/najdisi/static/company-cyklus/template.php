<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyRepoInterface;

/** @var PhpTemplateRendererInterface $this */
    /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );
    $companies = $companyRepo->findAll();
    $pStyle = ['style'=>'color: red;'];
    
    echo Html::p("Všechny company: events/v1/data/company", $pStyle);
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/company",
            ]
        );
    
    echo Html::tag('h4', $pStyle, "Cyklus pro všechny company");

    foreach ($companies as $company) {
        $companyId = $company->getId();
        echo Html::tag('div',['style'=>'background-color: yellow; color: red;'] ,$company->getName());
        
        echo Html::p("Jedna company s id company: events/v1/data/company/$companyId", $pStyle);
        echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/company/$companyId",
                ]
            );
        echo Html::p("Jedna adresa s id company (rodiče): events/v1/data/company/$companyId/companyaddress", $pStyle);
        echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/company/$companyId/companyaddress",
                ]
            );
        echo Html::p("Všechny kontakty jedné company s id company (rodiče): events/v1/data/company/$companyId/companycontact", $pStyle);
        echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/company/$companyId/companycontact",
                ]
            );

    }

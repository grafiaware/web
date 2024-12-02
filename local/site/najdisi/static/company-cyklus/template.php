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
    
    foreach ($companies as $company) {
        $companyId = $company->getId();

        echo Html::p("Jedna company s id company: events/v1/data/company/$companyId", $pStyle);
        echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/company/$companyId",
                ]
            );
        echo Html::p("Jedna adresa s id adresy: events/v1/data/companyAddress/$companyId", $pStyle);
        echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/companyAddress/$companyId",
                ]
            );
        echo Html::p("Všechny kontakty jedné company s id company (rodiče): events/v1/subdata/companyContact/$companyId", $pStyle);
        echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/subdata/companyContact/$companyId",
                ]
            );

    }

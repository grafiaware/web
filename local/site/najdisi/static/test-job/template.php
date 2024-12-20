<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Entity\JobInterface;

    /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );
    $companies = $companyRepo->findAll();
    /** @var JobRepoInterface $jobRepo */
    $jobRepo = $container->get(JobRepo::class);
    $pStyle = ['style'=>'color: red;'];

    echo Html::tag('h4', $pStyle, "tag - seznam");    
    echo Html::p("Všechny tagy: events/v1/data/tag", $pStyle);
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/tag",
            ]
        );
   
    echo Html::tag('h4', $pStyle, "job - cyklus pro všechny company");

    foreach ($companies as $company) {
        $companyId = $company->getId();
        $jobs = $jobRepo->find(" company_id = :idCompany ",  ['idCompany'=> $companyId ] );
        echo Html::tag('p',['class'=>'nadpis'] ,$company->getName());
//        echo Html::p("Všechny joby pro company s id $companyId: events/v1/data/job/{$job->getId()}", $pStyle);
        
        foreach ($jobs as $job) {
            /** @var JobInterface $job */
            echo Html::tag('div', 
                    [
                        'class'=>'cascade',
                        'data-red-apiuri'=>"events/v1/data/job/{$job->getId()}",
                    ]
                );            
        }

    }

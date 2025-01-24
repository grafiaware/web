<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

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
    
    ###
    # DUMMY data
    
        $isVisitor = false;
        $isVisitorDataPost = true;
        $isRepresentativeOfCompany = true;
        $visitorLoginName = 'visitor';
        $visitorJobRequestCount = 2;
    #
    ###
    
    foreach ($companies as $company) {
        $companyId = $company->getId();
        $jobs = $jobRepo->find(" company_id = :idCompany ",  ['idCompany'=> $companyId ] );
        echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/company/$companyId",
                ]
            );                 
        
        foreach ($jobs as $job) {
            include "pozice.php";
        }
    }      


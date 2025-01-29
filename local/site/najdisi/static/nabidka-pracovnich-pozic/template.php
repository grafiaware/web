<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Entity\CompanyInterface;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Entity\JobInterface;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Entity\VisitorJobRequestInterface;

use Events\Model\Repository\DocumentRepo;
use Events\Model\Repository\DocumentRepoInterface;

use Access\Enum\RoleEnum;



    
    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);
    $role = $statusViewModel->getUserRole();
    $loginName = $statusViewModel->getUserLoginName();
    $isVisitor = (isset($role) AND $role==RoleEnum::VISITOR);
    $representativeActions = $statusViewModel->getRepresentativeActions();
    $representative = isset($representativeActions) ? $representativeActions->getRepresentative() : null;
    /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );
    $companies = $companyRepo->findAll();
    /** @var JobRepo $jobRepo */
    $jobRepo = $container->get(JobRepo::class);    
    $jobs = $jobRepo->findAll();
    /** @var VisitorJobRequestRepo $jobRequestRepo */
    $jobRequestRepo = $container->get(VisitorJobRequestRepo::class);   
    /** @var DocumentRepoInterface $documentRepo */
    $documentRepo = $container->get(DocumentRepo::class );    
    

    
    ###
    # DUMMY data
    
//        $isVisitor = true;
//        $isVisitorDataPost = true;
//        $isRepresentativeOfCompany = true;
//        $visitorLoginName = 'visitor';
    #
    ###
    
    /** @var CompanyInterface $company */
    foreach ($companies as $company) {
        $companyId = $company->getId();
        $isRepresentativeOfCompany = isset($representative) && $companyId==($representative->getCompanyId());
        $companyJobs = $jobRepo->find(" company_id = :idCompany ",  ['idCompany'=> $companyId ] );
        
        echo Html::tag('div', 
                [
                    'class'=>'cascade nazev-firmy',
                    'data-red-apiuri'=>"events/v1/data/company/$companyId",
                ]
            );                 
        
        /** @var JobInterface $job */
        foreach ($companyJobs as $job) {
            $isVisitorDataPost = isset($loginName) && null!==$jobRequestRepo->get($loginName, $job->getId());
            $visitorJobRequestCount = count($jobRequestRepo->find( "job_id = :jobId ",  ['jobId'=> $job->getId()] ));
            include "pozice.php";  // cascade job a jobrequest
        }
    }      


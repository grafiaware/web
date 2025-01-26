<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Access\Enum\RoleEnum;

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;

use Events\Model\Repository\JobRepo;
use Events\Model\Entity\JobInterface;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Entity\VisitorJobRequestInterface;

use Events\Model\Repository\DocumentRepo;
use Events\Model\Repository\DocumentRepoInterface;

    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);
    $role = $statusViewModel->getUserRole();
    $loginName = $statusViewModel->getUserLoginName();
    $isVisitor = (isset($role) AND $role==RoleEnum::VISITOR);
    $representative = $statusViewModel->getRepresentativeActions()->getRepresentative();

    /** @var JobRepo $jobRepo */
    $jobRepo = $container->get(JobRepo::class);    
    $jobs = $jobRepo->findAll();
    /** @var VisitorJobRequestRepo $jobRequestRepo */
    $jobRequestRepo = $container->get(VisitorJobRequestRepo::class);   
    /** @var DocumentRepoInterface $documentRepo */
    $documentRepo = $container->get(DocumentRepo::class );
    
        $pStyle = ['style'=>'color: red;'];

    echo Html::tag('h4', $pStyle, "Přehled všech job requestů"); 
    /** @var JobInterface $job */
    foreach ($jobs as $job) {
        echo Html::tag('h4', [], $job->getNazev());
        echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/job/{$job->getId()}/jobrequest"
            ]
        );    
    }

    echo "Jen pro návštěvníka!";
    if ($isVisitor) {
        echo Html::tag('h4', $pStyle, "visitor - job requesty návštěvníka $loginName"); 
        $visitorJobRequests = $jobRequestRepo->find( "login_login_name = :loginLoginName ",  ['loginLoginName'=> $loginName] );
        /** @var VisitorJobRequestInterface $jobRequest */
        foreach ($visitorJobRequests as $jobRequest) {
            $job = $jobRepo->get($jobRequest->getJobId());  // jen pro název jobu
            echo Html::tag('h4', [], $job->getNazev());
            echo Html::tag('div', 
                    [
                        'class'=>'cascade',
                        'data-red-apiuri'=>"events/v1/data/job/{$jobRequest->getJobId()}/jobrequest/$loginName"
                    ]
                );
        }
    }    
    
    echo "Jen pro reprezentanta!";
    if (isset($representative)) {
        echo Html::tag('h4', $pStyle, "Job requesty reprezentanta {$representative->getLoginLoginName()}"); 
        $companyId = $representative->getCompanyId();
        // publikované i nepublikované - reprezentant vidí i odpublikované joby, na které se někdo hlásil
        $companyJobs = $jobRepo->find("company_id = :companyId", ['companyId' => $companyId]);
        /** @var JobInterface $job */
        foreach ($companyJobs as $job) {
            echo Html::tag('h4', [], $job->getNazev());
            echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/job/{$job->getId()}/jobrequest"
                ]
            );    
        }
    }        

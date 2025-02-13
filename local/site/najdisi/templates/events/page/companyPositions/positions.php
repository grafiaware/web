<?php
use Site\ConfigurationCache;

use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Repository\VisitorJobRequestRepo;
use Component\ViewModel\StatusViewModel;

    /** @var RepresentativeInterface $representative */
    $isRepresentative = isset($representative);
    $isRepresentativeOfCompany = $isRepresentative && $companyId==($representative->getCompanyId());
    /** @var VisitorJobRequestRepo $jobRequestRepo */
    $jobRequestRepo = $container->get(VisitorJobRequestRepo::class);   
    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);  
    
    $loginName = $statusViewModel->getUserLoginName();

            /** @var JobInterface $job */
            foreach ($jobs as $job) {
                $jobId = $job->getId();
                $isVisitorDataPost = isset($loginName) && null!==$jobRequestRepo->get($loginName, $jobId);
                $visitorJobRequestCount = count($jobRequestRepo->find( "job_id = :jobId ",  ['jobId'=> $jobId] ));
                
                ## proměnné pro position.php
                $isVisitor;
                $isRepresentative;
                $isRepresentativeOfCompany;
                $isVisitorDataPost;
                $visitorJobRequestCount;
                $block; // je chybně v contentNotLoggedIn
                
                $uriCascadeCompanyFamilyJob = "events/v1/data/company/$companyId/job/$jobId";
                $uriCascadeJobFamilyJobrequest = "events/v1/data/job/$jobId/jobrequest/$loginName";
                $uriCascadeJobFamilyJobrequestList = "events/v1/data/job/$jobId/jobrequest";
                include ConfigurationCache::eventTemplates()['templates']."page/position/position.php";
            }


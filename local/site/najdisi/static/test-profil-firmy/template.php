<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;
use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Entity\JobInterface;

/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);

/** @var  RepresentativeInterface $representativeFromStatus*/
$repreActions = $statusViewModel->getRepresentativeActions();
$representativeFromStatus = isset($repreActions) ? $repreActions->getRepresentative(): null;

if (isset($representativeFromStatus)) {
    $companyId = $representativeFromStatus->getCompanyId();
    /** @var JobRepoInterface $jobRepo */
//    $jobRepo = $container->get(JobRepo::class);
//    $jobs = $jobRepo->find(" company_id = :idCompany ",  ['idCompany'=> $companyId] );
    
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/company/$companyId",
            ]
        );
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/company/$companyId/companyaddress",
            ]
        );
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/company/$companyId/companycontact",
            ]
        );             
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/company/$companyId/companyinfo",
            ]
        ); 
    echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/company/$companyId/job",
                ]
            );
    
    
    
//    foreach ($jobs as $job) {
//        /** @var JobInterface $job */
//        echo Html::tag('div', 
//                [
//                    'class'=>'cascade',
//                    'data-red-apiuri'=>"events/v1/data/company/$companyId/job/{$job->getId()}",
//                ]
//            );            
//    }
    
} else {
    echo "Stránka je určena pouze přihlášenému reprezentantu firmy.";
}

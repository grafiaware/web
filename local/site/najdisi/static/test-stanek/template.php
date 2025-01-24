<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;
use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Repository\CompanyParameterRepoInterface;
use Events\Model\Repository\CompanyParameterRepo;
use Events\Model\Entity\CompanyParameterInterface;


/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
/** @var CompanyParameterRepoInterface $companyParameterRepo */
$companyParameterRepo = $container->get(CompanyParameterRepo::class);


/** @var  RepresentativeInterface $representativeFromStatus*/
$repreActions = $statusViewModel->getRepresentativeActions();
$representativeFromStatus = isset($repreActions) ? $repreActions->getRepresentative(): null;

if (isset($representativeFromStatus)) {
    $companyId = $representativeFromStatus->getCompanyId();
    /** @var  CompanyParameterInterface $companyParameter*/
    $companyParameter = $companyParameterRepo->get($companyId);
    if ( isset($companyParameter) ) {
        $jobLimit = $companyParameter->getJobLimit();
    }    
    
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/company/$companyId",
            ]
        );
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/company/$companyId/companyinfo",
            ]
        ); 
    if ( (isset($jobLimit) AND ($jobLimit>0) ) OR (!(isset($jobLimit)))  )  {     
        echo Html::tag('div', 
                    [
                        'class'=>'cascade',
                        'data-red-apiuri'=>"events/v1/data/company/$companyId/job",
                    ]
                );
    }
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/company/$companyId/companycontact",
            ]
        );
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/company/$companyId/companyaddress",
            ]
        );
    
    
} else {
    echo Html::p("Stránka je určena pouze přihlášenému reprezentantu firmy.", ["class"=>"ui blue segment"]);
    
}

<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;
use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Repository\CompanyParameterRepoInterface;
use Events\Model\Repository\CompanyParameterRepo;
use Events\Model\Entity\CompanyParameterInterface;
use Events\Model\Repository\JobRepo;

/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
/** @var CompanyParameterRepoInterface $companyParameterRepo */
$companyParameterRepo = $container->get(CompanyParameterRepo::class);


/** @var  RepresentativeInterface $representative*/
$repreActions = $statusViewModel->getRepresentativeActions();
$representative = isset($repreActions) ? $repreActions->getRepresentative(): null;

if (isset($representative)) {
    $companyId = $representative->getCompanyId();
    /** @var  CompanyParameterInterface $companyParameter*/
    $companyParameter = $companyParameterRepo->get($companyId);
    if ( isset($companyParameter) ) {
        $jobLimit = $companyParameter->getJobLimit();
    }    
    $editable = $repreActions->getDataEditable();

    echo Html::tag('div', 
            [
                'class'=>'cascade nazev-firmy',
                'data-red-apiuri'=>"events/v1/data/company/$companyId",
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
                'data-red-apiuri'=>"events/v1/data/company/$companyId/companycontact",
            ]
        );
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/company/$companyId/companyaddress",
            ]
        );
    
    if ( !isset($jobLimit) OR $jobLimit!=0)  {  
        if ($editable) {
            echo Html::tag('div', 
                    [
                        'class'=>'cascade',
                        'data-red-apiuri'=>"events/v1/data/company/$companyId/job",
                    ]
                );
        } else {
            /** @var JobRepoInterface $jobRepo */
            $jobRepo = $container->get(JobRepo::class);            
            $jobs = $jobRepo->find("company_id = :company_id ORDER BY nazev ", ['company_id' => $companyId]);     
       
            ### proměnné pro companyPositions
            $representative;   // representant - pokud se zobrazuje "jeho" firma, zobrazují se job requesty
            $jobs;  // pole jobů
            $companyId; // id zobrazované společnosti

            include ConfigurationCache::eventTemplates()['templates']."page/companyPositions/positions.php";            
        }
    }
    
} else {
    echo Html::p("Stránka je určena pouze přihlášenému reprezentantu firmy. Zapněte poprvé editaci.", ["class"=>"ui blue segment"]);
    
}

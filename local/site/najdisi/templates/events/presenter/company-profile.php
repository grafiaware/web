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


/** @var  CompanyParameterInterface $companyParameter*/
$companyParameter = $companyParameterRepo->get($companyId);
if ( isset($companyParameter) ) {
    $jobLimit = $companyParameter->getJobLimit();
}    

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
    echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/company/$companyId/job",
                ]
            );
}



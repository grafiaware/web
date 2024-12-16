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
    

    use Component\ViewModel\StatusViewModelInterface;
    use Component\ViewModel\StatusViewModel;
    use Auth\Model\Entity\LoginAggregateFullInterface;
    use Events\Model\Entity\RepresentativeInterface;


    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);
   
    /** @var  RepresentativeInterface $representativeFromStatus*/
    $repreActions = $statusViewModel->getRepresentativeActions();
    $representativeFromStatus = isset($repreActions) ? $repreActions->getRepresentative(): null;

    
    $companyId = $representativeFromStatus->getCompanyId();

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
                'data-red-apiuri'=>"events/v1/data/company/$companyId/job",
            ]
        );
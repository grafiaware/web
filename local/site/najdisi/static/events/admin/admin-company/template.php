<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyRepoInterface;

use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;
use Access\Enum\RoleEnum;

/** @var PhpTemplateRendererInterface $this */


/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$representativeActions = $statusViewModel->getRepresentativeActions();
$getEditable = isset($representativeActions) ? $representativeActions->getDataEditable() : false;
$userRole = $statusViewModel->getUserRole();
if ( $userRole == RoleEnum::EVENTS_ADMINISTRATOR AND $getEditable ) {

    /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );
    $companies = $companyRepo->findAll();
    $pStyle = ['style'=>'color: red;'];
    
    echo Html::tag('h4', $pStyle, "Cyklus pro všechny company");
        echo Html::tag('div', 
                [
                    'class'=>'cascade nazev-firmy',
                    'data-red-apiuri'=>"events/v1/data/company?version_fk=2026",
                ]
            );
        
} else {
    echo Html::p("Stránka je určena pouze pro administraci.", ["class"=>"ui orange segment"]);
    
}

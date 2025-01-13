<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Entity\JobInterface;

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;
use Events\Model\Entity\RepresentativeInterface;
use Access\Enum\RoleEnum;

/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);

/** @var  RepresentativeInterface $representativeFromStatus*/
$role = $statusViewModel->getUserRole();

if (isset($role) && $role==RoleEnum::REPRESENTATIVE) {
    echo Html::p("Přehled pracovních pozic je až do termínu konání veletrhu zobrazován pouze přihlášeným reprezentantům vystavovatelů a partnerů. Rozpracované informace o firmách a pozicích tak nejsou veřejné.", ["class"=>"ui segment"]);

    /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );
    $companies = $companyRepo->findAll();

    foreach ($companies as $company) {
        $companyId = $company->getId();
        echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/company/$companyId",
                ]
            );                 
        
            /** @var JobInterface $job */
            echo Html::tag('div', 
                    [
                        'class'=>'cascade',
                        'data-red-apiuri'=>"events/v1/data/company/$companyId/job",
                    ]
                );            

    }    
} else {
    echo "Stránka je až do termínu veletrhu zobrazena pouze přihlášeným reprezentantům firmy.";
}
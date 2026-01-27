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

$role = $statusViewModel->getUserRole();
$loginName = $statusViewModel->getUserLoginName();

if ($loginName=="Barbie25Girl" || $loginName=="JerryNoName" || $loginName=="visitor") {    

//if (isset($role) && ($role==RoleEnum::REPRESENTATIVE || $role==RoleEnum::EVENTS_ADMINISTRATOR)) {
    echo Html::p("Přehled je až do termínu konání veletrhu zobrazován pouze přihlášeným testerům. Rozpracované informace o firmách a pozicích tak nejsou veřejné.", ["class"=>"ui segment"]);
    ###########################
    $version = '2026';
    ###########################
    /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );
   
    $companies = $companyRepo->find( "version_fk=:version_fk order by name ASC ", [":version_fk"=>$version]) ;    

    foreach ($companies as $company) {
        $companyId = $company->getId();     
//        echo Html::p("Jedna company s id company: events/v1/data/company/$companyId", $pStyle);
        echo Html::tag('div', 
                [
                    'class'=>'cascade nazev-firmy',
                    'data-red-apiuri'=>"events/v1/data/company/$companyId",
                ]
            );
//        echo Html::p("Jedna adresa s id company (rodiče): events/v1/data/company/$companyId/companyaddress", $pStyle);
        echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/company/$companyId/companyaddress",
                ]
            );
//        echo Html::p("Všechny kontakty jedné company s id company (rodiče): events/v1/data/company/$companyId/companycontact", $pStyle);
        echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/company/$companyId/companycontact",
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
    echo Html::p("Stránka je až do termínu veletrhu zobrazena pouze přihlášeným reprezentantům firmy.", ["class"=>"ui blue segment"]);
    
}
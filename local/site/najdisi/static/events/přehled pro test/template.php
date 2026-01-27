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
        include ConfigurationCache::eventTemplates()['templates']."presenter/company-profile.php";
    }
} else {
    echo Html::p("Stránka je urečena pouze pro test.", ["class"=>"ui blue segment"]);
    
}

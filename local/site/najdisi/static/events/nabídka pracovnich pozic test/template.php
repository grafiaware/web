<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Auth\Middleware\Login\Controler\AuthControler;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobTagRepo;
use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Repository\JobToTagRepo;
use Events\Model\Repository\JobToTagRepoInterface;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Entity\VisitorJobRequestInterface;


use Events\Model\Entity\JobInterface;
use Events\Model\Entity\JobToTagInterface;
use Events\Model\Entity\Company;
use Events\Model\Entity\CompanyInterface;

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;

use Site\ConfigurationCache;

use Events\Middleware\Events\Controler\FilterControler;
use Access\Enum\RoleEnum;

/*
 * Zobrazuje seznam pracovních pozic pro jednotlivé společnosti.
 * 
 * Zobrazuje je společnosti, které mají nějakou pracovní pozici.
 */
    /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class ); 
    /** @var JobRepoInterface $jobRepo */
    $jobRepo = $container->get(JobRepo::class);
    /** @var JobTagRepoInterface $jobTagRepo */
    $jobTagRepo = $container->get(JobTagRepo::class);
    /** @var JobToTagRepoInterface $jobToTagRepo */
    $jobToTagRepo = $container->get(JobToTagRepo::class);    
    
    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);  
    $role = $statusViewModel->getUserRole();
    $loginName = $statusViewModel->getUserLoginName();
if ($loginName!=="Barbie25Girl" && $loginName!=="JerryNoName") {
    echo Html::p("Stránka je určena pouze pro testování před začátkem veletrhu.", ["class"=>"ui orange segment"]);
    
    
} else {    
    
    $isVisitor = (isset($role) AND $role==RoleEnum::VISITOR);
    $representativeActions = $statusViewModel->getRepresentativeActions();
    $representative = isset($representativeActions) ? $representativeActions->getRepresentative() : null;
    
    $presentationInfo = $statusViewModel->getPresentationInfos();
    $filter = $presentationInfo[FilterControler::FILTER] ?? null ; //bylo ulozeno v FilterControler->filterJob()
    if(isset($filter)) {
        $selectCompanyId = (int)$filter[FilterControler::FILTER_COMPANY];    
        $dataCheck = $filter[FilterControler::FILTER_TAGS];
    } else {
        $selectCompanyId = null;
    }
    
    $filterCheckboxData=[];
    if (!isset($dataCheck)) { 
        $filterCheckboxData=[];    
        $dataChecksForSelectA=[];
        $checksIdsIn = null;
    } else {
        $dataCheckForSelectA=[];
        $ii = 0;        
        foreach ($dataCheck as $key => $value) {
            $filterCheckboxData[FilterControler::FILTER_TAGS."[". $key .  "]"] = (int)$value;                                       
            $dataChecksForSelectA['in'.$ii++] = $value;        
        }
        if($dataChecksForSelectA) {
            $checksIdsIn = ":".implode(", :", array_keys($dataChecksForSelectA) );
        }
   }   
   
    //----------------------------------------------------
    
    ###########################
    $version = 'test';
    ###########################
   
    $allCompanies = $companyRepo->find( "version_fk=:version_fk order by name ASC ", [":version_fk"=>$version]) ;
//    $allCompanies = $companyRepo->find( "version_fk=:version order by name ASC ", [":version"=>$version]) ;

    $selectCompanies =[];    
    $selectCompanies [AuthControler::NULL_VALUE] =  "" ;
        /** @var CompanyInterface $company */ 
    foreach ( $allCompanies as $company ) {
        $selectCompanies [$company->getId()] = $company->getName() ;
    }                            
    //-------------------------------------------------------------------

    $tags = $jobTagRepo->findAll();
    foreach ($tags as $jobTag) {
       $filterCheckboxLabelsAndNameValuePairs[$jobTag->getTag()] = [FilterControler::FILTER_TAGS."[{$jobTag->getTag()}]" => $jobTag->getId()] ;
    }        
    if (isset($checksIdsIn)) {
        $jobToTagEntities = $jobToTagRepo->find( " job_tag_id in ($checksIdsIn) group by job_id " ,$dataChecksForSelectA );  // všechny vybrané (checked) a použité (jsou ve vazební tabulce)
    } else {
        // jen pro ty company, které mají nějaký tag
        $jobToTagEntities=$jobToTagRepo->find( " 1 group by job_id " ,$dataChecksForSelectA ); //  [];
    }
    
## view data
    $jobIds=[];
    $jobsInWithPlaceholders=[];
    $ii = 0;
    foreach ($jobToTagEntities as $jobToTagE) {
        $jobIds['in'.$ii++] = $jobToTagE->getJobId();  // id také jen pro company, které mají nějaké tagy
    }
    if($jobIds) {
        $jobsInWithPlaceholders = ":".implode(", :", array_keys($jobIds));  // 
    }

    if ( $selectCompanyId != 0 ) {
        $companies = $companyRepo->find(" id=:company_id ", ['company_id'=>$selectCompanyId]);      // ale vzdy vybrana jen jedna firma!        
    } else {
        $companies = $allCompanies;
    }


    $jobsCount = 0;
    /** @var CompanyInterface $company */
    foreach ($companies as $company) {
        if ($jobsInWithPlaceholders) {
            $companyJobs =  $jobRepo->find("id in ($jobsInWithPlaceholders)  AND company_id = :company_id AND published=1 ORDER BY nazev ",
                                              array_merge($jobIds, ['company_id' => $company->getId()]));  
        } else {
            $companyJobs = [];
        }
        $viewCompanies[] = ['company'=>$company, 'companyJobs'=>$companyJobs];
        $jobsCount = $jobsCount + count($companyJobs);
    }
    
## view    

    $isFilterVisible = ($selectCompanyId OR $filterCheckboxData);
    include('filtr.php');
    
    if ($jobsCount == 0) {
        echo Html::tag("div", ["class"=>"ui red segment"], Html::p("Zadanému fitru neodpovídá žádná položka." , []));                 
    } else {
        foreach ($viewCompanies as $viewCompany) {
            $jobs = $viewCompany['companyJobs'];   // joby jedné company
            if ($jobs) {
                $companyId = $viewCompany['company']->getId();
                $uriCascadeCompany = "events/v1/data/company/$companyId";
                echo Html::tag('div', ['class'=>'cascade nazev-firmy', 'data-red-apiuri'=>$uriCascadeCompany]);

                ### proměnné pro companyPositions
                $representative; $jobs; $companyId;
                include ConfigurationCache::eventTemplates()['templates']."page/companyPositions/positions.php";
            }
        }        
    }
}    

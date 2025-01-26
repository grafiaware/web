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

use Events\Model\Entity\JobInterface;
use Events\Model\Entity\JobToTagInterface;
use Events\Model\Entity\Company;
use Events\Model\Entity\CompanyInterface;

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;

use Events\Middleware\Events\Controler\FilterControler;
use Events\Model\Entity\RepresentativeInterface;
use Access\Enum\RoleEnum;

    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);        
    $filter = $statusViewModel->getPresentationInfos()[FilterControler::FILTER]; //bylo ulozeno v FilterControler->filterJob()
    
    $dataCheck = $filter['filterDataTags'];
    
    $dataCheckArr=[];
    if (!isset($dataCheck)) { 
        $dataCheckArr=[];    
        $dataChecksForSelectA=[];
        $checksIdsIn = null;
    }
    else {
        $dataCheckForSelectA=[];
        $ii = 0;        
        foreach ($dataCheck as $key => $value) {
            $dataCheckArr["filterDataTags[". $key .  "]"] = (int)$value;                                       
            
            $dataChecksForSelectA['in'.$ii++] = $value;        
        }
        if($dataChecksForSelectA) {
            $checksIdsIn = ":".implode(", :", array_keys($dataChecksForSelectA) );
        }
        //$dataCheckForSelectA $checksIdsIn   ---  se pouziji v sql            
   }   
   
    $selectCompanyId = (int)$filter['companyId'];
    //----------------------------------------------------
            
        /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );    
    $allCompanies = $companyRepo->find( " 1=1 order by name ASC ", []) ;
    $selectCompanies =[];    
    $selectCompanies [AuthControler::NULL_VALUE] =  "" ;
        /** @var CompanyInterface $company */ 
    foreach ( $allCompanies as $company ) {
        $selectCompanies [$company->getId()] = $company->getName() ;
    }                            
    //-------------------------------------------------------------------
       /** @var JobTagRepoInterface $jobTagRepo */
    $jobTagRepo = $container->get(JobTagRepo::class);
    $tags = $jobTagRepo->findAll();
    foreach ($tags as $tag) {
            $map[$tag->getId()] = $tag;
    }
    $allTags=[];
        // map jsou tagy indexované podle id tagů (se stejnou map byly renderovány items)
        /** @var JobTagInterface  $jobTag */
    foreach ( $map as $id => $jobTag) {
       $allTags[$jobTag->getTag()] = ["filterDataTags[{$jobTag->getTag()}]" => $jobTag->getId()] ;
    }        
    //-------------------------------------------------------------------     
//data  pro formular
//    $selectCompanyId_1 = 10;   
//    $dataCheckArr_1 = [ "filterDataTags[pro ZP]" => 53, 
//                        "filterDataTags[na rodičovské]" => 52 ];
    //-----------------------------------------------------------------------
?> 

    <div>Nastavte hodnoty pro výběr nabízených pracovních pozic:</div>
    
    <form class="ui huge form" action="" method="POST" > 
        
            <div class="field">
                <?= Html::select( 
                    "filterSelectCompany", 
                    "Firma:",  
                    [ "filterSelectCompany" => $selectCompanyId ],    
                    $selectCompanies ??  [] , 
                    []    // ['required' => true ],                        
                ) ?> 
            </div>
        
            <div class="field">
                 <div>Typ hledané pozice: </div>
                 <?= Html::checkbox( $allTags , $dataCheckArr ); ?>
            </div>
                                         
            <div>      
                <button class='ui secondary button' type='submit' 
                        formaction='events/v1/filterjob'> Vyhledat </button>
                <button class='ui secondary button' type='submit' 
                        formaction='events/v1/cleanfilterjob'> Vyčistit filtr </button>
            </div>                                    
    </form>
    
    
<?php
    /** @var JobToTagRepoInterface $jobToTagRepo */
    $jobToTagRepo = $container->get(JobToTagRepo::class);
    if (isset($checksIdsIn)) {
        $jobToTagEntities = $jobToTagRepo->find( " job_tag_id in ($checksIdsIn) group by job_id " ,$dataChecksForSelectA ); 
    } else {
        $jobToTagEntities=[];
    }
    
    //priprava pro in , jobsy
    $jobIds=[];
    $jobsIdsString=[];
    $ii = 0;
    foreach ($jobToTagEntities as $jobToTagE) {
        $jobIds['in'.$ii++] = $jobToTagE->getJobId();
    }
    if($jobIds) {
        $jobsIdsString = ":".implode(", :", array_keys($jobIds));
    }

    /** @var JobRepoInterface $jobRepo */
    $jobRepo = $container->get(JobRepo::class);
    if ( $selectCompanyId != 0 ) {
        $companies = $companyRepo->find(" id=:company_id ", ['company_id'=>$selectCompanyId]);      // ale vzdy vybrana jen jedna firma!
        if ($jobIds) {
           $compid = $companies[0]->getId(); 
           $jobEntities = $jobRepo->find( " id in ($jobsIdsString)  AND company_id = :company_id order by company_id, nazev " ,
                                          array_merge($jobIds, ['company_id' => $compid ]) );  
        } else {              
            $jobEntities = $jobRepo->find(" 1=1 order by company_id, nazev ASC ", []); 
            if (($dataChecksForSelectA) and (!($jobToTagEntities))) {
                $jobEntities = $jobRepo->find("  company_id = :company_id  order by company_id, nazev ASC ", ['company_id' => $compid ] );     
            } 
           
        }    
    } else {        
        $companies = $allCompanies; 
        if ($jobIds) {
           $jobEntities = $jobRepo->find( " id in ($jobsIdsString) order by company_id, nazev "  , $jobIds);             
        } else { 
           if ( $jobToTagEntities /*$dataChecksForSelectA*/) {
                $companies = [];
           }
           else { 
               $jobEntities = $jobRepo->find( " 1=1 order by company_id, nazev ASC ", [] );                 
           }
        }   
    }          

    
    if  ($companies ) {           
            /** @var CompanyInterface $comp */
        foreach ($companies as $keyC => $comp) {
            if  ($jobEntities ) {
                          /** @var JobInterface $job */
                foreach ($jobEntities as $keyJ => $job) {  
                    if ($comp->getId() == $job->getCompanyId()) {
                            //nadpisy
                        if (count($companies)!= 1 ) {             
                            if ($keyJ > 0) {
                                if ($jobEntities[$keyJ-1]->getCompanyId() <> ($jobEntities[$keyJ]->getCompanyId() ) ) {    
                                    echo Html::p($comp->getName(), []); 
                                }
                            } else {   
                                echo Html::p($comp->getName(), []); 
                            }           
                        }                
                        
                        echo Html::tag('div', 
                            [
                                'class'=>'cascade',
                                'data-red-apiuri'=>"events/v1/data/company/{$comp->getId()}/job/{$job->getId()}",
                            ]
                            );  
                    }                        
                } 
            }else {
                echo Html::p("Zadanému fitru neodpovídá žádná položka." , []); 
            }                     
        }    
    }
    else  {  
         echo Html::p("Zadanému fitru neodpovídá žádná položka." , []);                 
    }    

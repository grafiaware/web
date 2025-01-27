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
    $companies = $companyRepo->find( " 1=1 order by name ASC ", []) ;
    $selectCompanies =[];    
    $selectCompanies [AuthControler::NULL_VALUE] =  "" ;
        /** @var CompanyInterface $company */ 
    foreach ( $companies as $company ) {
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
    $filterVisible = ($selectCompanyId OR $dataCheckArr);
    
    
?> 

    <div><p class="podnadpis okraje">Nastavte hodnoty pro výběr nabízených pracovních pozic:</p></div>
    <div id="toggleFilter" class="ui big black button <?= $filterVisible ?? false ? 'active' : '' ?>">
        <i class="<?= $filterVisible ?? false ? 'close' : 'filter' ?> icon"></i> 
        <?= $filterVisible ?? false ? 'Skrýt filtr' : 'Zobrazit filtr' ?>
    </div>
    
    <div id="filterSection" class="ui segment">
        <form class="ui big form" action="" method="POST" > 
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
    </div>
    
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
    $joJobsIn=[];
    $ii = 0;
    foreach ($jobToTagEntities as $jobToTagE) {
        $jobIds['in'.$ii++] = $jobToTagE->getJobId();
    }
    if($jobIds) {
        $joJobsIn = ":".implode(", :", array_keys($jobIds));
    }

          /** @var JobRepoInterface $jobRepo */
    $jobRepo = $container->get(JobRepo::class);
    if ( $selectCompanyId != 0 ) {
        $comps = $companyRepo->find(" id=:company_id ", ['company_id'=>$selectCompanyId]);      // ale vzdy vybrana jen jedna firma!
        if ($jobIds) {
           $compid = $comps[0]->getId(); 
           $jobEntities = $jobRepo->find( " id in ($joJobsIn)  AND company_id = :company_id order by company_id, nazev " ,
                                          array_merge($jobIds, ['company_id' => $compid ]) );  
        } else {             
            $compid = $comps[0]->getId(); 
            $jobEntities = $jobRepo->find(" 1=1 order by company_id, nazev ASC ", []); 
            if (($dataChecksForSelectA) and (!($jobToTagEntities))) {
                //$jobEntities = $jobRepo->find("  company_id = :company_id  order by company_id, nazev ASC ", ['company_id' => $compid ] );     
                $jobEntities = [];     
            } 
           
        }    
    } else {        
        if ($jobIds) {
           $comps = $companies; 
           $jobEntities = $jobRepo->find( " id in ($joJobsIn) order by company_id, nazev "  , $jobIds);             
        } else { 
           $comps = $companies;             
           $jobEntities = $jobRepo->find( " 1=1 order by company_id, nazev ASC ", [] ); 
           if (($dataChecksForSelectA) and (!($jobToTagEntities))) {
                //$jobEntities = $jobRepo->find("  company_id = :company_id  order by company_id, nazev ASC ", ['company_id' => $compid ] );     
                $jobEntities = [];  
                $comps  = []; 
            } 
            
//           if ( $jobToTagEntities /*$dataChecksForSelectA*/) {  //bzlo
//                $comps = [];
//           }
//           else { 
//               $jobEntities = $jobRepo->find( " 1=1 order by company_id, nazev ASC ", [] );                 
//           }
        }   
    }          


    
    if  ($comps ) {           
            /** @var CompanyInterface $comp */
        foreach ($comps as $keyC => $comp) {
            if  ($jobEntities ) {
                          /** @var JobInterface $job */
                foreach ($jobEntities as $keyJ => $job) {  
                    if ($comp->getId() == $job->getCompanyId()) {
                           
                        //nadpisy
                        if (count($comps)!= 1 ) {             
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

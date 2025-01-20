<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Auth\Middleware\Login\Controler\AuthControler;

use Events\Model\Entity\Company;
use Events\Model\Entity\CompanyInterface;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobTagRepo;
use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Repository\JobToTagRepo;
use Events\Model\Repository\JobToTagRepoInterface;
use Events\Model\Entity\JobToTagInterface;

use Events\Model\Repository\JobRepo;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Entity\JobInterface;


use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;
use Events\Middleware\Events\Controler\FilterControler;

use Events\Model\Entity\RepresentativeInterface;
use Access\Enum\RoleEnum;

/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
        
    $filter = $statusViewModel->getPresentationInfos()[FilterControler::FILTER]; //bylo ulozeno v FilterControler->filterJob()
    
    $dataCheckNew=[];         
    $dataCheck = $filter['filterDataTags'];
    if (!isset($dataCheck)) { 
        $dataCheckNew=[];     
        $dataCheckForSelect='';
    }
    else {
        foreach ($dataCheck as $key => $value) {
            $dataCheckNew["filterDataTags[". $key .  "]"] = (int)$value;      
            $dataCheckForSelect .= $value . ',';
        }
    }
    $dataCheckArr = $dataCheckNew;
    
    $checksForSelect = substr($dataCheckForSelect,0,strlen($dataCheckForSelect)-1);    
    //----------------------------------
    
    $selectCompanyId = (int)$filter['companyId'];
    //----------------------------------
            
        /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );    
    $companies = $companyRepo->findAll();
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
    //------------------------------------------------------------------------
    
    
  
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
    
        $jobToTagRepo = $container->get(JobToTagRepo::class);
        $jobToTagEntities = $jobToTagRepo->find( " job_tag_id in (:str) group by job_id " ,[ str => $checksForSelect ] );
                       /** @var CompanyInterface $co */
        foreach ($companies as $key => $co) { 
             
        }      
         foreach ($companies as $key => $co) { 
             
        }   
    
        
    if ( ($selectCompanyId == 0 ) and ($checksForSelect == '') ) {  //pro vsechny firmy vsechny joby
           /** @var CompanyInterface $co */
        foreach ($companies as $key => $co) {    
          echo Html::p($co->getName(), []);
          echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/company/{$co->getId()}/job",
            ]
            );   
        }   
    }
    
    if ( ($selectCompanyId == 0 ) and ($checksForSelect != '') ) {    //pro vsechny firmy vyber joby
               /** @var CompanyInterface $co */
        foreach ($companies as $key => $co) {  
            
            echo Html::p($co->getName(), []);         
                /** @var JobToTagRepoInterface $jobToTagRepo */
            $jobToTagRepo = $container->get(JobToTagRepo::class);
            $jobToTagEntities = $jobToTagRepo->find( " job_tag_id in (:str) group by job_id " ,[ str => $checksForSelect ] );
         
            
                /** @var JobToTagInterface $jobToTagE */
            foreach ($jobToTagEntities as $jobToTagE) {
                
                
                echo '<div>' . $jobToTagE->getJobId() . '</div>' ;
                $jobId = $jobToTagE->getJobId();
                    /** @var JobInterface $job */
                echo Html::tag('div', 
                    [
                        'class'=>'cascade',
                        'data-red-apiuri'=>"events/v1/data/company/{$co->getId()}/job/$jobId",
                    ]
                    );            
            }    
        }
        
        
        
        
        
            ##################
        if ( $selectCompanyId != 0 ) {
            $comp = $companyRepo->find("company_id=:company_id", ['company_id'=>$selectCompanyId]);
        } else {
            $comp= $companies;
        }            
        /** @var CompanyInterface $co */
        foreach ($comp as $key => $co) {  

            echo Html::p($co->getName(), []);         
                /** @var JobToTagRepoInterface $jobToTagRepo */
            $jobToTagRepo = $container->get(JobToTagRepo::class);
            $jobToTagEntities = $jobToTagRepo->find( " job_tag_id in (:str) group by job_id " ,[ str => $checksForSelect ] );            
            foreach ($jobToTagEntities as $jobToTagE) {
                $jobIds[] = $jobToTagE->getJobId();
            }
            $jojo = implode(", ", $jobIds);
            $jobRepo = $container->get(JobRepo::class);
            $jobEntities = $jobRepo->find( " job_id in (:jojo) AND company_id=:company_id" ,[ 'jojo' => $jojo, 'company_id'=>$co->getId() ] );  
            foreach ($jobEntities as $job) {                
                echo Html::tag('div', 
                    [
                        'class'=>'cascade',
                        'data-red-apiuri'=>"events/v1/data/company/{$co->getId()}/job/{$job->getId()}",
                    ]
                    );            
            }               
        }
        
        
        
        
        
        
        
    }
    
    if ( $selectCompanyId != 0 and $checksForSelect != '' ) { //pro 1 firmu  vyber joby
         /** @var CompanyInterface $comp */
        $comp = $companyRepo->get($selectCompanyId);
        echo Html::p($comp->getName(), []);    
        
            /** @var JobToTagRepoInterface $jobToTagRepo */   
        $jobToTagRepo = $container->get(JobToTagRepo::class);
        $jobToTagEntities = $jobToTagRepo->find( " job_tag_id in (:str) group by job_id " ,[ str => $checksForSelect ] );
            /** @var JobToTagInterface $jobToTagE */
        foreach ($jobToTagEntities as $jobToTagE) {
            echo '<div>' . $jobToTagE->getJobId() . '</div>' ;
            $jobId = $jobToTagE->getJobId();
            echo Html::tag('div', 
                [
                    'class'=>'cascade',
                    'data-red-apiuri'=>"events/v1/data/company/$selectCompanyId/job/$jobId",
                ]
                );            
        }
    
    
    }
    
    if ( $selectCompanyId != 0 and $checksForSelect = '' ) { //pro 1 firmu vsechny joby
        
        echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/data/company/$selectCompanyId/job",
        ]
        );          
    
    }    
 
    
//    else {
//       ?> 
<!--        <p> Zadanému fitru neodpovídá žádná položka.  </p>-->
    <?php
//    }
    
   
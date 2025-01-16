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

if (isset($role) && ($role==RoleEnum::REPRESENTATIVE || $role==RoleEnum::EVENTS_ADMINISTRATOR)) {

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
            $allTags[$jobTag->getTag()] = ["data[{$jobTag->getTag()}]" => $jobTag->getId()] ;
    }        
    //------------------------------------------------------------------- 
        
    $selectCompanyId = 10;   
    $checkValuesArr = [ "data[pro ZP]" => 53,  "data[na rodičovské]" => 52 ];
    ?> 

  
    <form class="ui huge form" action="" method="POST" > 
        
            <div class="field">
                <?= Html::select( 
                    "selectCompany", 
                    "Firma pro filtr:",  
                    [ "selectCompany"=> $selectCompanyId  ],    
                    $selectCompanies ??  [] , 
                    []    // ['required' => true ],                        
                ) ?> 
            </div>
        
            <div class="field">
                 <?= Html::checkbox( $allTags , $checkValuesArr ); ?>
            </div>
                                         
            <div>      
                <button class='ui secondary button' type='submit' 
                        formaction='events/v1/filterjob'> Uložit </button>
                <button class='ui secondary button' type='submit' 
                        formaction='events/v1/filterjobclear'> Odškrtnout vše </button>
            </div>            
        
        <?php
//                       echo Html::tag('span', 
//                       [
//                       'class'=>'cascade',
//                       'data-red-apiuri'=> "events/v1/data/jobtotag",
//                       ]
//                       );         
        ?>
           
    </form>
    
    
    
    
    
    
    
    
 <?php       

//    /** @var CompanyRepoInterface $companyRepo */
//    $companyRepo = $container->get(CompanyRepo::class );
//    $companies = $companyRepo->findAll();
//
//    foreach ($companies as $company) {
//        $companyId = $company->getId();
//        echo Html::tag('div', 
//                [
//                    'class'=>'cascade',
//                    'data-red-apiuri'=>"events/v1/data/company/$companyId",
//                ]
//            );                 
//        
//            /** @var JobInterface $job */
//            echo Html::tag('div', 
//                    [
//                        'class'=>'cascade',
//                        'data-red-apiuri'=>"events/v1/data/company/$companyId/job",
//                    ]
//                );            
//       }    
          
          
} else {
    echo Html::p("Stránka je až do termínu veletrhu zobrazena pouze přihlášeným reprezentantům firmy.", ["class"=>"ui blue segment"]);
    
}
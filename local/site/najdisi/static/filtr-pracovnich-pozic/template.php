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
    
//    $selectCompanies =[];    //
//    $selectCompanies [''] =  "vyber - povinné pole" ;
//    $companyEntities = $companyRepo->findAll(); //
//        /** @var CompanyInterface $comp */ 
//    foreach ( $companyEntities as $comp) {
//        $selectCompanies [$comp->getId()] =  $comp->getName() ;
//    }
    
    
    $companies = $companyRepo->findAll();
    $selectCompanies =[];    

    $selectCompanies [AuthControler::NULL_VALUE] =  "" ;
    /** @var CompanyInterface $company */ 
    foreach ( $companies as $company ) {
        $selectCompanies [$company->getId()] = $company->getName() ;
    }    
    
    
    
    
//     $roles = $roleRepo->findAll();
//    $selectRoles = [];
//    
//    $selectRoles [AuthControler::NULL_VALUE] =  "" ;
//    /** @var RoleInterface $role */ 
//    foreach ( $roles as $role ) {
//        $selectRoles [$role->getRole()] = $role->getRole() ;
//    }    
    
    
    
    
    
    
    ?> 

  


        <div class="field">
                    
                              <?= Html::select(
                                "selectCompany", 
                                "Firma - Company name:",
                                [ ],
                                $selectCompanies, 
                                ['required' => true ],
                                '' 
                                      ) ?>       
            
        </div> 
        <div class="field">
                    <?= Html::select( 
                        "selectCompany", 
                        "--v--",  
                        ["selectCompany"=>10 ],    
                        $selectCompanies ??  [] , 
                        []
                        ) ?> 
        </div>
    
    
    
    
    
    
    
    
    
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
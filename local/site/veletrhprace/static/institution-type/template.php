<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;
use Status\Model\Repository\StatusSecurityRepo;
use Auth\Model\Entity\LoginAggregateFullInterface;

//use Events\Model\Repository\CompanyRepo;
//use Events\Model\Repository\CompanyContactRepo;
//use Events\Model\Repository\InstitutionRepo;
//use Events\Model\Repository\InstitutionRepo;
use Events\Model\Repository\InstitutionTypeRepo;

//use Events\Model\Entity\CompanyInterface;
//use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\InstitutionTypeInterface;
use Events\Model\Entity\InstitutionType;

/** @var PhpTemplateRendererInterface $this */


$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateFullInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();

//if (isset($loginAggregate)) {  
 
     /** @var InstitutionTypeRepo $institutionTypeRepo */
    $institutionTypeRepo = $container->get(InstitutionTypeRepo::class );
            
    //------------------------------------------------------------------    
//        $selectInstitutionType =[];    
//        $institutionTypeEntities = $institutionTypeRepo->findAll();
//            /** @var InstitutionTypeInterface $entity */ 
//        foreach ( $institutionTypeEntities as $entity) {
//            $selectInstitutionType [$entity->getId()] =  $entity->getInstitutionType() ;
//        }                 
//    
        $institutionType=[];
        $institutionTypeEntities = $institutionTypeRepo->findAll();
        
        if ($institutionTypeEntities) {       
             /** @var InstitutionTypeInterface $institutionTypeE */ 
            foreach ($institutionTypeEntities as $institutionTypeE) {
                
                //$institutionTypE = $institutionTypeRepo->get( $entity->getInstitutionTypeId() );
                //$type = $institutionType->getInstitutionType();
                
                /** @var InstitutionInterface $entity */
                $institutionType[] = [                                        
                    'institutionTypeId' => $institutionTypeE->getId(),                    
                    'institutionType' => $institutionTypeE->getInstitutionType()
//                    'selectInstitutionTypeId' =>  $selectInstitutionType
                    ];
            }   
        } 
                              
       // $selecty['selectInstitutionTypeId'] = $selectInstitutionType;       
        
  ?>


    <div>
    <div class="ui styled fluid accordion">   

            Typ instituce <hr/>                      
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/institution-type.php',  $institutionType)  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej další typ instituce
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/institution-type.php' ) ?>     
                </div>                  
            </div>            
    </div>
    </div>

  <?php     
//    } else { ?>
          <div>
          </div>   
  <?php 
//    }
//   
//} 
//else{
//     echo  "Údaje o typu institutice smí vidět jen přihlášený." ; 
//}
    
   ?>
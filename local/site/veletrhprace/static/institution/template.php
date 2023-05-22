<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;
use Status\Model\Repository\StatusSecurityRepo;
use Auth\Model\Entity\LoginAggregateFullInterface;

use Events\Model\Repository\InstitutionRepo;
use Events\Model\Repository\InstitutionTypeRepo;

use Events\Model\Entity\InstitutionInterface;
use Events\Model\Entity\Institution;

use Events\Middleware\Events\Controler\EventControler_2;

/** @var PhpTemplateRendererInterface $this */


$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateFullInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();

//if (isset($loginAggregate)) {


//    /** @var CompanyRepo $companyRepo */ 
//    $companyRepo = $container->get(CompanyRepo::class );
//    /** @var CompanyContactRepo $companyContactRepo */
//    $companyContactRepo = $container->get(CompanyContactRepo::class );
//    /** @var RepresentativeRepo $representativeRepo */
//    $representativeRepo = $container->get(RepresentativeRepo::class );
    
    /** @var InstitutionRepo $institutionRepo */
    $institutionRepo = $container->get(InstitutionRepo::class );
     /** @var InstitutionTypeRepo $institutionTypeRepo */
    $institutionTypeRepo = $container->get(InstitutionTypeRepo::class );
            
    //------------------------------------------------------------------    
        $selectInstitutionType =[];    
        $selectInstitutionType [EventControler_2::NULL_VALUE_nahradni] =  "" ;
        $institutionTypeEntities = $institutionTypeRepo->findAll();
            /** @var InstitutionTypeInterface $entity */ 
        foreach ( $institutionTypeEntities as $entity) {
            $selectInstitutionType [$entity->getId()] =  $entity->getInstitutionType() ;
        }                 
    
        $institutions=[];
        $institutionsEntities = $institutionRepo->findAll();
        if ($institutionsEntities) {         
            foreach ($institutionsEntities as $entity) {
                
                $pom = $entity->getInstitutionTypeId() ?? '';
                $institutionTypE = $institutionTypeRepo->get( $entity->getInstitutionTypeId() ?? '' );
                $type = isset ($institutionTypE) ? $institutionTypE->getInstitutionType()  : '';                
                
                /** @var InstitutionInterface $entity */
                $institutions[] = [
                    'institutionId' => $entity->getId(),
                    'name' =>  $entity->getName(),
                    'institutionTypeId' => $entity->getInstitutionTypeId() ?? ''   /*$entity->getInstitutionTypeId()*/,
                    'institutionType' =>   $type, 
                    'selectInstitutionTypeId' =>  $selectInstitutionType
                    ];
            }   
        } 
                              
        $selecty['selectInstitutionTypeId'] = $selectInstitutionType;       
        
  ?>


    <div>
    <div class="ui styled fluid accordion">   

            Instituce <hr/>                      
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/institution.php',  $institutions)  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej další instituci
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/institution.php', $selecty ) ?>                                                                                 
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
//     echo  "Údaje o instituticích smí vidět jen přihlášený." ; 
//}
    
   ?>
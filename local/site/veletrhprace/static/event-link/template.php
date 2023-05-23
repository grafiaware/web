<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;
use Status\Model\Repository\StatusSecurityRepo;
use Auth\Model\Entity\LoginAggregateFullInterface;


use Events\Model\Repository\EventLinkPhaseRepo;
use Events\Model\Entity\EventLinkPhaseInterface;
use Events\Model\Entity\EventLinkPhase;
use Events\Model\Repository\EventLinkRepo;
use Events\Model\Entity\EventLinkInterface;
use Events\Model\Entity\EventLink;

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
    
    /** @var EventLinkRepo $eventLinkRepo */
    $eventLinkRepo = $container->get(EventLinkRepo::class );
     /** @var EventLinkPhaseRepo $EventLinkPhaseRepo */
    $EventLinkPhaseRepo = $container->get(EventLinkPhaseRepo::class );
            
    //------------------------------------------------------------------    
    
    
    
    
    
    
        $selectEventLinkPhase =[];    
        $eventLinkPhaseEntities = $eventLinkPhaseRepo->findAll();
            /** @var EventLinkPhaseInterface $entity */ 
        foreach ( $eventLinkPhaseEntities as $entity) {
            $selectEventLinkPhase [$entity->getId()] =  $entity->getText() ;
        }                 
        
        
    
        $eventLinks=[];
        **
        $institutionsEntities = $institutionRepo->findAll();
        if ($institutionsEntities) {         
            foreach ($institutionsEntities as $entity) {
                
                $institutionTypE = $institutionTypeRepo->get( $entity->getInstitutionTypeId() );
                $type = $institutionTypE->getInstitutionType();
                
                /** @var InstitutionInterface $entity */
                $institutions[] = [
                    'institutionId' => $entity->getId(),
                    'name' =>  $entity->getName(),
                    'institutionTypeId' => $entity->getInstitutionTypeId(),
                    'institutionType' => ($institutionTypeRepo->get( $entity->getInstitutionTypeId() ) )->getInstitutionType(),
                    'selectInstitutionTypeId' =>  $selectInstitutionType
                    ];
            }   
        } 
                              
        $selecty['selectInstitutionTypeId'] = $selectInstitutionType;       
        
  ?>


    <div>
    <div class="ui styled fluid accordion">   

            Odkazy pro události <hr/>                      
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/event-link.php',  $institu)  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej další odkaz
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/event-link.php', $selecty ) ?>                                                                                 
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
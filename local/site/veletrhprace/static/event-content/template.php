<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\EventContentTypeRepoInterface;
use Events\Model\Repository\EventContentTypeRepo;
use Events\Model\Entity\EventContentTypeInterface;

use Events\Model\Repository\EventContentRepoInterface;
use Events\Model\Repository\EventContentRepo;
use Events\Model\Entity\EventContentInterface;

use Events\Model\Repository\InstitutionRepoInterface;
use Events\Model\Repository\InstitutionRepo;
use Events\Model\Entity\InstitutionInterface;

use Events\Middleware\Events\Controler\EventControler_2;

/** @var PhpTemplateRendererInterface $this */

    /** @var EventContentTypeRepoInterface $eventContentTypeRepo */ 
    $eventContentTypeRepo = $container->get(EventContentTypeRepo::class );
    /** @var EventContentRepoInterface $eventContentRepo */
    $eventContentRepo = $container->get(EventContentRepo::class );
    /** @var InstitutionRepoInterface $institutionRepo */
    $institutionRepo = $container->get(InstitutionRepo::class );
    
    
    //------------------------------------------------------------------            

    $idInstitution = 23;
    //------------------------------------------------------------------
 
    $institutionIdFk  = $idInstitution;
    
    $selectContentTypes = [];
    $selectContentTypes [EventControler_2::NULL_VALUE_nahradni] =  "" ;
    $allContentType = $eventContentTypeRepo->findAll();
    $allContentTypeArray=[];
    /** @var  EventContentInterface $type */
    foreach ($allContentType as $type) {    
        $contype ['type'] = $type->getType();
        $contype ['name'] = $type->getName();               
        $allContentTypeArray[] = $contype;    
        
        $selectContentTypes [$type->getType()] =  $type->getName() ;
    }
    //$selectContentTypes [EventControler_2::NULL_VALUE] =  "" ;  
  
    
    $selectInstitutions = [];
    $selectInstitutions [EventControler_2::NULL_VALUE_nahradni] =  "" ;
    $institutionEntities = $institutionRepo->findAll();
        /** @var InstitutionInterface $inst */ 
    foreach ( $institutionEntities as $inst ) {
        $selectInstitutions [$inst->getId()] =  $inst->getName() ;
    }
    //$selectInstitutions [EventControler_2::NULL_VALUE] =  "" ;
    
    
    $selecty = [];
    $selecty['selectInstitutions'] = $selectInstitutions;
    $selecty['selectContentTypes'] = $selectContentTypes;
        
    $institutionE1 = $institutionRepo->get($institutionIdFk) ;  
    $iNameE1 = $institutionE1->getName();
    
    //---------------------------------------------------------
    // Contenty pro  $idInstitution
    $eventContentEntities = $eventContentRepo->find( " institution_id_fk = :institutionIdFk ",  ['institutionIdFk'=> $institutionIdFk /*'23'*/] );
    if ($eventContentEntities) {   
            /** @var EventContentInterface $entity */
            foreach ($eventContentEntities as $entity) {
                      $nu1 = $entity->getInstitutionIdFk();
                      $nu2 = $entity->getEventContentTypeFk();
                
                $institutionE = $institutionRepo->get($entity->getInstitutionIdFk()) ;               
                $eventContents[] = [
                    'institutionIdFk' => ($entity->getInstitutionIdFk()) ?? EventControler_2::NULL_VALUE_nahradni , 
                    'selectInstitutions' => $selectInstitutions, 
                    'institutionName' => $institutionE->getName(),
                    
                    'eventContentTypeFk' => ($entity->getEventContentTypeFk()) ?? EventControler_2::NULL_VALUE_nahradni,
                    'selectContentTypes' => $selectContentTypes, 
                    
                    'title' =>  $entity->getTitle(),
                    'perex' =>  $entity->getPerex(),
                    'party' =>  $entity->getParty(),
                    'idContent' =>  $entity->getId()
                    ];
            }   
    }                                          
  ?>
    
    <div class="ui styled fluid accordion">           
        <div>                
           <b>Obsahy událostí (event content) pro instituci -  <?= $iNameE1 ?> </b>
        </div>                          
        ------------------------------------------------------                      
        <div>      
            <?php  if (isset ($eventContents) ) {   ?> 
                <?= $this->repeat(__DIR__.'/event-content.php',  $eventContents )  ?> 
            <?php  }   ?>
            
            <br>
            <div>                   
                Přidej další obsah události (event content)
            </div>  
            <div>     
                <?= $this->insert( __DIR__.'/event-content.php', [ "selectInstitutions" => $selectInstitutions,
                                                                   "institutionIdFk" => $institutionIdFk,
                                                                   "institutionName"=> $iNameE1,
                    
                                                                   "selectContentTypes" => $selectContentTypes,
                                                                   "eventContentTypeFk" => EventControler_2::NULL_VALUE_nahradni                   
                                                                 ] ) ?>                                                                                 
            </div>                  
        </div>           
                                      
    </div>


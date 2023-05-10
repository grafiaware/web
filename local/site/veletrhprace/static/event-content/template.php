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
    
    $selectContentType = [];
    $allContentType = $eventContentTypeRepo->findAll();
    $allContentTypeArray=[];
    /** @var  EventContentInterface $type */
    foreach ($allContentType as $type) {    
        $contype ['type'] = $type->getType();
        $contype ['name'] = $type->getName();               
        $allContentTypeArray[] = $contype;    
        
        $selectContentType [$type->getType()] =  $type->getName() ;
    }
    
        
    $selecty = [];
    $selectInstitution = [];
    $institutionEntities = $institutionRepo->findAll();
        /** @var InstitutionInterface $inst */ 
    foreach ( $institutionEntities as $inst ) {
        $selectInstitution [$inst->getId()] =  $inst->getName() ;
    }
    
    $selecty['selectInstitutions'] = $selectInstitution;
    $selecty['selectContentTypes'] = $selectContentType;
     
    
  
    // Contenty pro  $idInstitution
    $eventContentEntities = $eventContentRepo->find( " institution_id_fk = :institutionIdFk ",  ['institutionIdFk'=> $institutionIdFk /*'23'*/] );
    if ($eventContentEntities) {   
            /** @var EventContentInterface $entity */
            foreach ($eventContentEntities as $entity) {
                $institutionE = $institutionRepo->get($entity->getInstitutionIdFk()) ;               
                $eventContents[] = [
                    'institutionIdFk' => $entity->getInstitutionIdFk(), 
                    'selectInstitutions' => $selectInstitution, 
                    'institutionName' => $institutionE->getName(),
                    
                    'eventContentTypeFk' => $entity->getEventContentTypeFk(),
                    'selectContentTypes' => $selectContentType, 
                    
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
           <b>Obsahy událostí (event content)</b>
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
                <?= $this->insert( __DIR__.'/event-content.php', [ "selectInstitutions" => $selectInstitution,
                                                                   "institutionIdFk" => $institutionIdFk,
                    
                                                                   "selectContentTypes" => $selectContentType,
                                                                   "eventContentTypeFk" => ""                    
                                                                 ] ) ?>                                                                                 
            </div>                  
        </div>           
                                      
    </div>


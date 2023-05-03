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
 
    
    
 
    $allContentType = $eventContentTypeRepo->findAll();
    $allContentTypeArray=[];
    /** @var  EventContentInterface $type */
    foreach ($allContentType as $type) {    
        $contype ['type'] = $type->getType();
        $contype ['name'] = $type->getName();               
        $allContentTypeArray[] = $contype;       
    }
    
    
    
    
    // Contenty pro institution 23
    $eventContentEntities = $eventContentRepo->find(  " institution_id_fk = :institutionIdFk ",  ['institutionIdFk'=> '23' ]);
    if ($eventContentEntities) {   
            /** @var EventContentInterface $entity */
            foreach ($eventContentEntities as $entity) {
                $institutionE = $institutionRepo->get($entity->getInstitutionIdFk()) ;               
                $eventContents[] = [
                    'institutionIdFk' => $entity->getId(),
                    'institutionName' => $institutionE->getName(),
                    'eventContentTypeFk' => $entity->getEventContentTypeFk(),
                    
                    'title' =>  $entity->getTitle(),
                    'perex' =>  $entity->getPerex(),
                    'party' =>  $entity->getParty(),
                    'idI' =>  $entity->getId()
                    ];
            }   
    }                                     
    
             
  ?>

    
    <div class="ui styled fluid accordion">   
        
        <div>                
           <b>Obsah eventu (události)</b>
        </div>                   
        <div>
           <!--  < ?= /* $this->repeat(__DIR__.'/job-tagSeznam.php', $allTagsString, 'seznam')  */ ? > -->
        </div>
        ------------------------------------------------------      
        
        
        <div>      
            <?= $this->repeat(__DIR__.'/event-content.php',  $eventContents )  ?> )
            
            
            <div>                   
                Přidej další obsah eventu (události)
            </div>  
            <div>     
                <?= $this->insert( __DIR__.'/event-content.php' ) ?>                                                                                 
            </div>                  
        </div>           
                                      
    </div>


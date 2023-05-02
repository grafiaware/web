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
    
    
    
    
    //VYBRAT Content pro institutio 23
    
    
    
    
    
    
    
    
             
  ?>

    
    <div class="ui styled fluid accordion">   
        
        <div>                
           <b>Typ obsahu eventu</b>
        </div>                   
        <div>
           <!--  < ?= /* $this->repeat(__DIR__.'/job-tagSeznam.php', $allTagsString, 'seznam')  */ ? > -->
        </div>
        ------------------------------------------------------        
        
         <div>      
            <?= $this->repeat(__DIR__.'/event-content-type.php',  $allContentTypeArray)  ?>
            <div>                   
                Přidej další typ
            </div>  
            <div>     
                <?= $this->insert( __DIR__.'/event-content-type.php' ) ?>                                                                                 
            </div>                  
        </div>           
                                      
    </div>


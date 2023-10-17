<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\EventContentTypeRepoInterface;
use Events\Model\Repository\EventContentTypeRepo;
use Events\Model\Entity\EventContentTypeInterface;

/** @var PhpTemplateRendererInterface $this */

    /** @var EventContentTypeRepoInterface $eventContentTypeRepo */ 
    $eventContentTypeRepo = $container->get(EventContentTypeRepo::class );
    //------------------------------------------------------------------
 
    $allContentType = $eventContentTypeRepo->findAll();
    $allContentTypeArray=[];
    /** @var  EventContentInterface $type */
    foreach ($allContentType as $type) {   
        $contype ['id'] = $type->getId();
        $contype ['type'] = $type->getType();
        $contype ['name'] = $type->getName();               
        $allContentTypeArray[] = $contype;       
    }
             
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


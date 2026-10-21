<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Events\Middleware\Events\ViewModel\EventContentTypeViewModel;

/** @var PhpTemplateRendererInterface $this */

/** @var EventContentTypeViewModel $eventContentTypeViewModel */
$eventContentTypeViewModel = $container->get(EventContentTypeViewModel::class);
$eventContentTypesArray = $eventContentTypeViewModel->getEventTypesArray();

?>
    
    <div class="ui styled fluid accordion">         
        <div>                
           <b>Typ obsahu eventu</b>
        </div>                   
        ------------------------------------------------------        
        
         <div>      
            <?= $this->repeat(__DIR__.'/event-content-type.php',  $eventContentTypesArray)  ?>
            <div>                   
                Přidej další typ
            </div>  
            <div>     
                <?= $this->insert( __DIR__.'/event-content-type.php' ) ?>                                                                                 
            </div>                  
        </div>           
    </div>


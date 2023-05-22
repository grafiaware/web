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

/** @var PhpTemplateRendererInterface $this */


$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateFullInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();

//if (isset($loginAggregate)) {  




 
     /** @var EventLinkPhaseRepo $eventLinkPhaseRepo */
    $eventLinkPhaseRepo = $container->get(EventLinkPhaseRepo::class );
            
    //------------------------------------------------------------------    
          
    
        $eventLinkPhase=[];
        $eventLinkPhaseEntities = $eventLinkPhaseRepo->findAll();
        
        if ($eventLinkPhaseEntities) {       
             /** @var EventLinkPhaseInterface $eventLinkPhaseE */ 
            foreach ($eventLinkPhaseEntities as  $eventLinkPhaseE ) {
                $eventLinkPhase[] = [                                        
                    'eventLinkPhaseId' => $eventLinkPhaseE->getId(),                    
                    'eventLinkPhaseText' => $eventLinkPhaseE->getText() ?? '' 
                    ];
            }   
        } 
                                     
  ?>


    <div>
    <div class="ui styled fluid accordion">   

            Fáze odkazu pro události <hr/>                      
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/event-link-phase.php',  $eventLinkPhase)  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej další fázi (fáze odkazu pro událost)
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/event-link-phase.php' ) ?>     
                </div>                  
            </div>            
    </div>
    </div>

    
   ?>
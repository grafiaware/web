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
    
    /** @var EventLinkRepo $eventLinkRepo */
    $eventLinkRepo = $container->get(EventLinkRepo::class );
     /** @var EventLinkPhaseRepo $EventLinkPhaseRepo */
    $eventLinkPhaseRepo = $container->get(EventLinkPhaseRepo::class );
            
    //------------------------------------------------------------------    
            
    
    
    
    
        $selectEventLinkPhase =[];  
        $selectEventLinkPhase [''] =  "tady musis" ;
        $eventLinkPhaseEntities = $eventLinkPhaseRepo->findAll();
        if (isset($eventLinkPhaseEntities) ) {
            /** @var EventLinkPhaseInterface $ent */ 
            foreach ( $eventLinkPhaseEntities as $ent) {
                $selectEventLinkPhase [$ent->getId()] =  $ent->getText() ?? '' ;
            }                         
        } 
    
        $eventLinks=[];      
       // $AR=[];
        $eventLinksEntities = $eventLinkRepo->findAll();
        if ($eventLinksEntities) {      
            /** @var EventLinkInterface $entity */
            foreach ($eventLinksEntities as $entity) {
                /** @var EventLinkPhaseInterface $eventLinkPhaseE */
                $eventLinkPhaseE = $eventLinkPhaseRepo->get( $entity->getLinkPhaseIdFk() ?? '' ) ;
                $phaseText = isset ($eventLinkPhaseE) ? $eventLinkPhaseE->getText()  : '' ;
                
                $eventLinks[] = [
                    'eventLinkId' => $entity->getId(),
                    'show' => boolval($entity->getShow())  /*??  EventControler_2::NULL_VALUE_nahradni*/ ,
                    'href' =>  $entity->getHref(),
                    
                    'eventLinkPhaseIdFk' => $entity->getLinkPhaseIdFk() ??  EventControler_2::NULL_VALUE_nahradni , 
                    'eventLinkPhaseText' => $phaseText,                  
                    'selectEventLinkPhase' => $selectEventLinkPhase
                    ];
            }   
        } 
//        else {
//             $eventLinks[] = [ 
//                 'selectEventLinkPhase' => $selectEventLinkPhase
//             ];
//        }
                              
       // $selecty['selectEventLinkPhase'] = $selectEventLinkPhase;         
  ?>


    <div>
    <div class="ui styled fluid accordion">   

            Odkazy pro události <hr/>                      
            <div class="active content">                    
                <?php  if (isset ($eventLinks) ) {   ?> 
                    <?= $this->repeat(__DIR__.'/event-link.php',  $eventLinks)  ?>             
                <?php  }   ?>                

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej další odkaz
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/event-link.php', [
                                                            'selectEventLinkPhase' => $selectEventLinkPhase,
                                                            'eventLinkPhaseIdFk' =>  EventControler_2::NULL_VALUE_nahradni,
                    ] ) ?>                                                                                 
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
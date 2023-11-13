<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

//use Events\Model\Repository\EventContentTypeRepoInterface;
//use Events\Model\Repository\EventContentTypeRepo;
//use Events\Model\Entity\EventContentTypeInterface;
//
//use Events\Model\Repository\EventContentRepoInterface;
//use Events\Model\Repository\EventContentRepo;
//use Events\Model\Entity\EventContentInterface;
//
//use Events\Model\Repository\InstitutionRepoInterface;
//use Events\Model\Repository\InstitutionRepo;
//use Events\Model\Entity\InstitutionInterface;
//
//use Events\Middleware\Events\Controler\EventControler_2;

use Auth\Model\Repository\CredentialsRepoInterface;
use Auth\Model\Repository\CredentialsRepo;




/** @var PhpTemplateRendererInterface $this */

    /** @var CredentialsRepoInterface $credentialsRepo */ 
    $credentialsRepo = $container->get(CredentialsRepo::class );
    
    
    dfdf
    
    
//     = [];
//    $selectContentTypes [''] =  "vyber - povinné pole" ;
//    $allContentType = $eventContentTypeRepo->findAll();
//    $eventContentTypesArray=[];
//    /** @var  EventContentInterface $type */
//    foreach ($allContentType as $type) {    
//        $contype ['type'] = $type->getType();
//        $contype ['name'] = $type->getName();               
//        $eventContentTypesArray[] = $contype;    
//        
//        $selectContentTypes [$type->getId()] =  $type->getName()  ;
//    }
  
    
    $selectRoles = [];
    
    
    $selectInstitutions [EventControler_2::NULL_VALUE_nahradni] =  "" ;
    $institutionEntities = $institutionRepo->findAll();
        /** @var InstitutionInterface $inst */ 
    foreach ( $institutionEntities as $inst ) {
        $selectInstitutions [$inst->getId()] =  $inst->getName() ;
    }
    
    
    $selecty = [];
    $selecty['selectInstitutions'] = $selectInstitutions;
    $selecty['selectContentTypes'] = $selectContentTypes;
           
    
    //---------------------------------------------------------
    // Contenty pro  $idInstitution
    //$eventContentEntities = $eventContentRepo->find( " institution_id_fk = :institutionIdFk ",  ['institutionIdFk'=> $institutionIdFk /*'23'*/] );
    
    // Contenty všechny
    $eventContentEntities = $eventContentRepo->findAll();

    if ($eventContentEntities) {   
            /** @var EventContentInterface $entity */
            foreach ($eventContentEntities as $entity) {
                      $nu1 = $entity->getInstitutionIdFk();
                      $nu2 = $entity->getEventContentTypeIdFk();
                
                $institutionE = $institutionRepo->get(  ($entity->getInstitutionIdFk()) ?? ''    ) ;               
                $eventContents[] = [
                    'institutionIdFk' => ($entity->getInstitutionIdFk()) ?? EventControler_2::NULL_VALUE_nahradni , 
                    'selectInstitutions' => $selectInstitutions, 
                    'institutionName' => ( isset($institutionE) ? $institutionE->getName() : '' ),
                    
                    'eventContentTypeIdFk' => ($entity->getEventContentTypeIdFk()), /*?? EventControler_2::NULL_VALUE_nahradni,*/
                    'selectContentTypes' => $selectContentTypes, 
                    
                    'title' =>  $entity->getTitle(),
                    'perex' =>  $entity->getPerex(),
                    'party' =>  $entity->getParty(),
                    'idContent' =>  $entity->getId()
                    ];
            }   
    }
//    else {
//        $eventContents[] = [
//             'selectInstitutions' => $selectInstitutions, 
//             'selectContentTypes' => $selectContentTypes, 
//        ];
//    }
                                              
  ?>
    
    <div class="ui styled fluid accordion">           
        <div>             
           <b>CREDENTIALS</b>
           
           
           <b>Obsahy událostí (event content) </b>
        </div>                          
        ------------------------------------------------------                      
        <div>      
            <?php  if (isset ($eventContents) ) {   ?> 
                <?= $this->repeat(__DIR__.'/event-content.php',  $eventContents )  ?> 
            <?php  }   ?>
            
            <br>
            <div>                   
               !+++! Přidej další obsah události (event content)
            </div>  
            <div>     
                <?= $this->insert( __DIR__.'/event-content.php', [ "selectInstitutions" => $selectInstitutions,
                                                                   "institutionIdFk" =>  EventControler_2::NULL_VALUE_nahradni,                                                                  
                    
                                                                   "selectContentTypes" => $selectContentTypes,
                                                                   "eventContentTypeIdFk" =>  ''//not null
                                                                 ] ) ?>                                                                                 
            </div>                  
        </div>           
                                      
    </div>


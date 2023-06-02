<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;
use Status\Model\Repository\StatusSecurityRepo;
use Auth\Model\Entity\LoginAggregateFullInterface;


use Events\Model\Repository\InstitutionTypeRepo;


use Events\Model\Entity\InstitutionTypeInterface;
use Events\Model\Entity\InstitutionType;

/** @var PhpTemplateRendererInterface $this */


$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateFullInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();

//if (isset($loginAggregate)) {  
 
     /** @var InstitutionTypeRepo $institutionTypeRepo */
    $institutionTypeRepo = $container->get(InstitutionTypeRepo::class );
            
    //------------------------------------------------------------------    
              
        $institutionType=[];
        $institutionTypeEntities = $institutionTypeRepo->findAll();
        
        if ($institutionTypeEntities) {       
             /** @var InstitutionTypeInterface $institutionTypeE */ 
            foreach ($institutionTypeEntities as $institutionTypeE) {               
                $institutionType[] = [                                        
                    'institutionTypeId' => $institutionTypeE->getId(),                    
                    'institutionType' => $institutionTypeE->getInstitutionType()
                    ];
            }   
        }        
        
  ?>


    <div>
    <div class="ui styled fluid accordion">   

            Typ instituce <hr/>                      
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/institution-type.php',  $institutionType)  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej další typ instituce
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/institution-type.php' ) ?>     
                </div>                  
            </div>            
    </div>
    </div>


    
   ?>
<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Status\Model\Repository\StatusSecurityRepo;

use Pes\Text\Text;
use Pes\Text\Html;


use Events\Model\Repository\LoginRepo;
use Events\Model\Entity\LoginInterface;

use Events\Model\Repository\InstitutionTypeRepo;
use Events\Model\Entity\InstitutionTypeInterface;

/** @var PhpTemplateRendererInterface $this */


    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();
    /** @var LoginAggregateFullInterface $loginAggregate */
    $loginAggregate = $statusSecurity->getLoginAggregate();

//    if (isset($loginAggregate)) {
//        $loginName = $loginAggregate->getLoginName();
//        $cred = $loginAggregate->getCredentials();
//        
//        $role = $loginAggregate->getCredentials()->getRole() ?? '';
//        if ($role = 'sup') {
    
// ------------------------------------------------
   
    /** @var InstitutionTypeRepo $institutionTypeRepo */ 
    $institutionTypeRepo = $container->get(InstitutionTypeRepo::class );
                  
            
        $institutionType=[];
        $institutionTypeEntities = $institutionTypeRepo->findAll();
        if ($institutionTypeEntities) {         
            foreach ($institutionTypeEntities as $typeEntity) {
                /** @var InstitutionTypeInterface $typeEntity */
                $institutionType[] = [
                    'institutionTypeId' => $typeEntity->getId(),
                    'institutionType' =>  $typeEntity->getInstitutionType()                               
                    ];
            }   
        }             
        
  ?>


    <div>
    <div class="ui styled fluid accordion">   

            <div>
                Institution Type <hr/>
            </div>     
        
            <div>      
                <?= $this->repeat(__DIR__.'/institution-type.php',  $institutionType)  ?>
            </div> 
            
            <div >
                Přidej další  typ                             
                <?= $this->insert( __DIR__.'/institution-type.php') ?>                                                                                 
            </div>                  
                     
    </div>
    </div>

  <?php
//        }
//        else {  echo  "Údaje  smí vidět jen přihlášený, a to s rolí 'sup'." ; }
//        
//    }
//    else {  echo  "Údaje  smí vidět jen přihlášený, a to s rolí 'sup'." ;
//        
//    }
  ?>
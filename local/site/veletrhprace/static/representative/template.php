<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Status\Model\Repository\StatusSecurityRepo;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyAddressRepo;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\CompanyAddressInterface;

/** @var PhpTemplateRendererInterface $this */



    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();
    /** @var LoginAggregateFullInterface $loginAggregate */
    $loginAggregate = $statusSecurity->getLoginAggregate();

    if (isset($loginAggregate)) {
        $loginName = $loginAggregate->getLoginName();
        $cred = $loginAggregate->getCredentials();
        
        $role = $loginAggregate->getCredentials()->getRole() ?? '';
    }

    
    /** @var CompanyRepo $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    
    //------------------------------------------------------------------
              
    

//    $idCompany = 10 ;
//    
//    /** @var CompanyInterface $companyEntity */ 
//    $companyEntity = $companyRepo->get($idCompany);
//    if ($companyEntity) {       
//            
//        $companyAddress=[];
//        /** @var CompanyAddressInterface $companyAddressEntity */
//        $companyAddressEntity = $companyAddressRepo->get($idCompany);
//        if ($companyAddressEntity) {           
//            $companyAddress = [
//                'companyId' => $idCompany,
//                'name'   => $companyAddressEntity->getName(),
//                'lokace' => $companyAddressEntity->getLokace(),
//                'psc'    => $companyAddressEntity->getPsc(),
//                'obec'   => $companyAddressEntity->getObec()
//                ];
//        }    
//        else {
//            $companyAddress = [
//                'companyId' => $idCompany
//                ];
//        }   
            
        
  ?>

    <div>
    <div class="ui styled fluid accordion">   
                    
            <div class="active title">
                <i class="dropdown icon"></i>
              Zadej Representative vystavovatele 
              <?= $loginName ?>
            </div>                        
            <div class="active content">      
               <!-- < ?= $this->insert(__DIR__.'/company-address.php',  $companyAddress)  ? >     -->     
            </div>                
        
    </div>
    </div>

 


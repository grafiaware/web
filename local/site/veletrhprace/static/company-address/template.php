<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyAddressRepo;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\CompanyAddressInterface;

/** @var PhpTemplateRendererInterface $this */
   
    
    /** @var CompanyRepo $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var CompanyAddressRepo $companyAddressRepo */ 
    $companyAddressRepo = $container->get(CompanyAddressRepo::class );
    //------------------------------------------------------------------

    $idCompany = 10 ;
    
    //dalo by se zjistit vsechny  company, kde je prihlaseny representatntem
    //        if ( $representativeRepo->findByLogin($loginName) )  --neni metoda 
            
    //------------------------------------------------------------------
    
    /** @var CompanyInterface $companyEntity */ 
    $companyEntity = $companyRepo->get($idCompany);
    if ( isset($companyEntity) ) {       
            
        $companyAddress=[];
        /** @var CompanyAddressInterface $companyAddressEntity */
        $companyAddressEntity = $companyAddressRepo->get($idCompany);
        if ($companyAddressEntity) {           
            $companyAddress = [
                'companyId'=> $idCompany,
                'companyIdA' => $idCompany,               
                'name'   => $companyAddressEntity->getName(),
                'lokace' => $companyAddressEntity->getLokace(),
                'psc'    => $companyAddressEntity->getPsc(),
                'obec'   => $companyAddressEntity->getObec()
                ];
        }    
        else {
            $companyAddress = [
                'companyId' => $idCompany
                ];
        }   
            
        
  ?>

    <div>
    <div class="ui styled fluid accordion">
            
            Vystavovatel (company): |* <?= $companyEntity->getName(); ?> *|                   
            <div class="active title">
                <i class="dropdown icon"></i>
                Adresa vystavovatele
            </div>                        
            <div class="active content">      
                <?= $this->insert(__DIR__.'/company-address.php',  $companyAddress)  ?>          
            </div>                
        
    </div>
    </div>

   <?php     
    } else { ?>
          <div>
          </div>   
  <?php 
   }
  ?>


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

    $idCompany = 25 ;
    
    //dalo by se zjistit vsechny  company, kde je prihlaseny representatntem
    //        if ( $representativeRepo->findByLogin($loginName) )  --neni metoda 
            
    //------------------------------------------------------------------
    
    /** @var CompanyInterface $company */ 
    $company = $companyRepo->get($idCompany);
    if ( isset($company) ) {       
            
        $companyAddress=[];
        /** @var CompanyAddressInterface $companyAddress */
        $companyAddress = $companyAddressRepo->get($idCompany);
        if ($companyAddress) {           
            $companyAddress = [
                'companyId'=> $idCompany,
                'name'   => $companyAddress->getName(),
                'lokace' => $companyAddress->getLokace(),
                'psc'    => $companyAddress->getPsc(),
                'obec'   => $companyAddress->getObec()
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
        
        Vyžaduje přihlášení. <br/>        
            Vystavovatel (company): |* <?= $company->getName(); ?> *|                   
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



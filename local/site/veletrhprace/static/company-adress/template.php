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
    
    /** @var CompanyInterface $companyEntity */ 
    $companyEntity = $companyRepo->get($idCompany);
    if ($companyEntity) {       
            
        $companyAddress=[];
        /** @var CompanyAddressInterface $companyAddressEntity */
        $companyAddressEntity = $companyAddressRepo->get($idCompany);
        if ($companyAddressEntity) {           
            $companyAddress = [
                'companyId' => $idCompany,
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
    }
  ?>



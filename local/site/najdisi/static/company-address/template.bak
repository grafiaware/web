<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyAddressRepo;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\CompanyAddressInterface;
use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;

/** @var PhpTemplateRendererInterface $this */
    
    /** @var CompanyRepo $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var CompanyAddressRepo $companyAddressRepo */ 
    $companyAddressRepo = $container->get(CompanyAddressRepo::class );
    //------------------------------------------------------------------
    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);
    $representativeFromStatus = $statusViewModel->getRepresentativeActions()->getRepresentative();
    $loginName = isset($representativeFromStatus) ? $representativeFromStatus->getLoginLoginName() : null;
    $idCompany = isset($representativeFromStatus) ? $representativeFromStatus->getCompanyId() : null ; 
    //---------- $idCompany je company prihlaseneho representanta
    
    if ( isset($idCompany) ) {       
        /** @var CompanyInterface $company */ 
        $company = $companyRepo->get($idCompany);            
        /** @var CompanyAddressInterface $companyAddressEntity */
        $companyAddressEntity = $companyAddressRepo->get($idCompany);
        if (isset($companyAddressEntity)) {           
            $companyAddress = [
                'companyId'=> $idCompany,
                'companyId_proAdress'=> $idCompany,
                'name'   => $companyAddressEntity->getName(),
                'lokace' => $companyAddressEntity->getLokace(),
                'psc'    => $companyAddressEntity->getPsc(),
                'obec'   => $companyAddressEntity->getObec()
                ];
        }    
        else {
            $companyAddress = [
                'companyId'=> $idCompany,  // id potřebné pro insert nové adresy
                ];
        }   
            
        
  ?>

    <div>
    <div class="ui styled fluid accordion">
        
        Vyžaduje přihlášení.    <?= isset($loginName)? " - přihlášen $loginName " : "" ; ?>   <br/>        
        Firma (company): |*     <?= isset($company)? $company->getName() : "" ; ?> *|           
                        
            <div class="active title">
                <i class="dropdown icon"></i>
                Adresa firmy  <?= $company->getName(); ?>    
            </div> 
            
            <div class="active content">      
                <?= $this->insert(__DIR__.'/company-address.php',  $companyAddress)  ?>                               
            </div>
    </div>
    </div>

   <?php     
    } else { ?>
          <div>
          Vyžaduje přihlášení.    <?= isset($loginName)? " - přihlášen $loginName " : ""  ; ?>   <br/>        
          Firma (company): |*     <?= isset($company)? $company->getName() : "" ;  ?>     *|      
          </div>   
  <?php 
   }
  ?>



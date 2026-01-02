<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyContactRepoInterface;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\CompanyContactInterface;
use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;


/** @var PhpTemplateRendererInterface $this */

    /** @var CompanyRepoInterface $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var CompanyContactRepoInterface $companyContactRepo */
    $companyContactRepo = $container->get(CompanyContactRepo::class );

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
                 
        $companyContacts=[];
        $companyContactEntities = $companyContactRepo->find( " company_id = :idCompany ",  ['idCompany'=> $idCompany ] );
        if ($companyContactEntities) {         
             /** @var CompanyContactInterface $cCEntity */
            foreach ($companyContactEntities as $cCEntity) {               
                $companyContacts[] = [
                    'companyContactId' => $cCEntity->getId(),
                    'companyId' => $cCEntity->getCompanyId(),
                    'name' =>  $cCEntity->getName(),
                    'phones' =>  $cCEntity->getPhones(),
                    'mobiles' =>  $cCEntity->getMobiles(),
                    'emails' =>  $cCEntity->getEmails()
                    ];
            }   
        }             
        
  ?>


    <div>
    <div class="ui styled fluid accordion">   
            
        Vyžaduje přihlášení.    <?= isset($loginName)? " - přihlášen $loginName " : "" ; ?>   <br/>        
        Firma (company): |*     <?= isset($company)? $company->getName() : "" ; ?> *|       
            
            <div class="active title">
                <i class="dropdown icon"></i>
                Kontakty firmy <?= $company->getName(); ?>
            </div>                        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-contact.php',  $companyContacts)  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej kontakt firmy
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/company-contact.php', ['companyId' => $idCompany] ) ?>                                                                                 
                </div>                  
            </div>            
    </div>
    </div>

  <?php     
    } else { ?>
          <div>
            Vyžaduje přihlášení.    <?= isset($loginName)? " - přihlášen $loginName " : ""  ; ?>   <br/>        
            Firma (company): |*     <?= isset($company)? $company->getName() : "" ;  ?> *|        
          </div>   
  <?php 
   }
  ?>
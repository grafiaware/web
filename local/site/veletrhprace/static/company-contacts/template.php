<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\CompanyContactInterface;

use Events\Model\Repository\RepresentativeRepo;

/** @var PhpTemplateRendererInterface $this */

    /** @var CompanyRepo $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var CompanyContactRepo $companyContactRepo */
    $companyContactRepo = $container->get(CompanyContactRepo::class );
    /** @var RepresentativeRepo $representativeRepo */
    $representativeRepo = $container->get(RepresentativeRepo::class );
    //------------------------------------------------------------------

    $idCompany = 10;         
                                           
    //dalo by se zjistit vsechny  company, kde je prihlaseny representatntem
    //        if ( $representativeRepo->findByLogin($loginName) )   --neni metoda 
                               
    
    //------------------------------------------------------------------
    
    /** @var CompanyInterface $company */ 
    $company = $companyRepo->get($idCompany);
    if (isset ($company)) {       
            
        $companyContacts=[];
        $companyContactEntities = $companyContactRepo->find( " company_id = :idCompany ",  ['idCompany'=> $idCompany ] );
        if ($companyContactEntities) {         
            foreach ($companyContactEntities as $cCEntity) {
                /** @var CompanyContactInterface $cCEntity */
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

            Vystavovatel (company): |* <?= $company->getName(); ?> *|
            <div class="active title">
                <i class="dropdown icon"></i>
                Kontakty vystavovatele 
            </div>                        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-contact.php',  $companyContacts)  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej další kontakt vystavovatele
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
          </div>   
  <?php 
   }
  ?>
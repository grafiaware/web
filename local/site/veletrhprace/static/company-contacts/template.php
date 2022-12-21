<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\CompanyContactInterface;

/** @var PhpTemplateRendererInterface $this */

    /** @var CompanyRepo $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var CompanyContactRepo $companyContactRepo */
    $companyContactRepo = $container->get(CompanyContactRepo::class );
    //------------------------------------------------------------------

    $idCompany = 10;
    
    /** @var CompanyInterface $companyEntity */ 
    $companyEntity = $companyRepo->get($idCompany);
    if ($companyEntity) {       
            
        $companyContactEntities = $companyContactRepo->find( " company_id = :idCompany ",  ['idCompany'=> $idCompany ] );
        $companyContacts=[];
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
            
        
  ?>


    <div>
    <div class="ui styled fluid accordion">   

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
    }
  ?>
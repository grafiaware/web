<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Repository\RepresentativeRepo;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\RepresentativaInterface;


/** @var PhpTemplateRendererInterface $this */




    /** @var RepresentativeRepo $representativeRepo */
    $representativeRepo = $container->get(RepresentativeRepo::class );
    /** @var CompanyRepo $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var CompanyContactRepo $companyContactRepo */
    $companyContactRepo = $container->get(CompanyContactRepo::class );
    /** @var CompanyAddressRepo $companyAddressRepo */ 
    $companyAddressRepo = $container->get(CompanyAddressRepo::class );
    //------------------------------------------------------------------
                    
        $companiesContacts=[];       
         /**  @var CompanyInterface $company */             
        $companyEntities = $companyRepo->findAll();
        
        foreach ($companyEntities as  $company ){            
            $companyContactEntities = $companyContactRepo->find( " company_id = :id ",  ['id'=> $company->getId()] );           
            
            $companyContacts=[];
            /**  @var CompanyContactInterface $cntct */
            foreach ($companyContactEntities as $cntct) {
                /** @var CompanyContactInterface $cntct */
                $companyContacts[] = [
                    'companyContactId' => $cntct->getId(),
                    'companyId' => $cntct->getCompanyId(),
                    'name' =>  $cntct->getName(),
                    'phones' =>  $cntct->getPhones(),
                    'mobiles' =>  $cntct->getMobiles(),
                    'emails' =>  $cntct->getEmails()
                    ];
            }
            $companiesContacts[] = $companyContacts;

        }  


?>







           <?= $this->repeat(__DIR__.'/company-contact.php', $companyContacts  )  ?>




            <div class="active title">
                <i class="dropdown icon"></i>
                Kontakty vystavovatele
            </div>                        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/content/company/company-contact.php', $companyContacts  )  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej další kontakt vystavovatele
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/content/company/company-contact.php', [ 'companyId' => $idCompanyFromRepresentative ] ) ?>                                                                                 
                </div>                  
            </div>            
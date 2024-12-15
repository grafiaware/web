<?php
use Template\Compiler\TemplateCompilerInterface;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;
use Access\Enum\RoleEnum;

use Status\Model\Entity\StatusSecurity;
use Status\Model\Repository\StatusSecurityRepo;
use Events\Model\Entity\RepresentativeInterface;

use Events\Model\Repository\EnrollRepo;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyContactRepoInterface;
use Events\Model\Repository\CompanyAddressRepoInterface;

use Events\Middleware\Events\ViewModel\JobViewModel;

use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyContactInterface;

use Pes\Text\Html;
use Pes\Text\Text;

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;
use Auth\Model\Entity\LoginAggregateFullInterface;


    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);
   
    // $role = $statusViewModel->getUserRole();
    /** @var  RepresentativeInterface $representativeFromStatus*/
    $repreActions = $statusViewModel->getRepresentativeActions();
    $representativeFromStatus = isset($repreActions) ? $repreActions->getRepresentative(): null;
      
    
    $readonly = 'readonly="1"';
    $disabled = 'disabled="1"';
    
//    $editable =  ( ($statusViewModel->getUserRole()===RoleEnum::EVENTS_ADMINISTRATOR) /*OR
//                   ($statusViewModel->getUserRole()===RoleEnum::)*/  );
//                //$readonly = 'readonly="1"';
//                //$disabled = 'disabled="1"';
//    if ( isset($editable) ) {
//        $readonly = '';
//        $disabled = '';
//    } else {
//        $readonly = 'readonly';
//        $disabled = 'disabled';
//    }

              
        //--------------------- repo ----------------------------------------      
        /** @var CompanyRepoInterface $companyRepo */
        $companyRepo = $container->get(CompanyRepo::class );
        /** @var CompanyContactRepoInterface $companyContactRepo */
        $companyContactRepo = $container->get(CompanyContactRepo::class );
        /** @var CompanyAddressRepoInterface $companyAddressRepo */
        $companyAddressRepo = $container->get(CompanyAddressRepo::class );
        //------------------------------------------------------------------ 
       
//        //########## potreba v obou ifech
//        if  ( isset($representativeFromStatus) ) {
//            $idCompany = $representativeFromStatus->getCompanyId(); 
//            $companyE = $companyRepo->get($idCompany);  
//        }    

        
        
        
    //##########------- jen representant vystavovatele (konkretni firmy)  -----------
    if  ( isset($representativeFromStatus) ) {                
                    
        //--------------------- $loginAggregate ---------------
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
        /** @var StatusSecurity $statusSecurity */
        $statusSecurity = $statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregate */
        $loginAggregate = $statusSecurity->getLoginAggregate();
        //------------------------------------------------------
        //##########   
        $idCompany = $representativeFromStatus->getCompanyId(); 
        $companyE = $companyRepo->get($idCompany);

        /** @var JobViewModel $jobModel */
        $jobModel = $container->get( JobViewModel::class );

        
        //--- pro  profil.php  -- include
        $representativeContext =  [  //representative a company
            'logNameRepresentative' =>  $representativeFromStatus->getLoginLoginName(),
            'idCompany' =>  $companyE->getId(),
            'nameCompany' =>  $companyE->getName(),
            ];            
        $registation = $loginAggregate->getRegistration();
        $representativeContext ['regmail'] = isset($registation) ? $registation->getEmail() : "";       //BERU Z REGISTRACE doplnen mail                            
           
     
        //--- pro company  vypsat vsechny companyContact      
        $companyContactEntities = $companyContactRepo->find( " company_id = :idCompany ",  
                                                             ['idCompany' => $companyE->getId()] );
        $companyContacts=[];
        foreach ($companyContactEntities as $companyContact) {
            /** @var CompanyContactInterface $companyContact */
            $companyContacts[] = [
                'companyContactId' => $companyContact->getId(),
                'companyId' => $companyContact->getCompanyId(),
                'name' =>  $companyContact->getName(),
                'phones' =>  $companyContact->getPhones(),
                'mobiles' =>  $companyContact->getMobiles(),
                'emails' =>  $companyContact->getEmails()
                ];
        }
        //--- pro company vypsat companyAddress
        $companyAddress=[];
        /** @var CompanyAddressInterface $companyAddress */
        //$companyAddress = $companyAddressRepo->get( $idCompanyZPresenterPerson );
        $companyAddressE = $companyAddressRepo->get( $idCompany );
        if ($companyAddressE) {
            $companyAddress = [
                'companyId'=> $companyAddressE->getCompanyId(),
                'name'   => $companyAddressE->getName(),
                'lokace' => $companyAddressE->getLokace(),
                'psc'    => $companyAddressE->getPsc(),
                'obec'   => $companyAddressE->getObec()
                ];                                                                                                                                                                                                                                                                                                                                                                                                                                      
        }
        else {
            $companyAddress = ['companyId' =>   $idCompany]  ;    //  $idCompanyZPresenterPerson ];
        }
        
        //--- jobsy pro company tohoto representative
        //TODO: odstranit předávání kontejneru - potřebuje ho vypis-pozic\pozice_2.php   
        foreach ($jobModel->getCompanyJobList($idCompany  /*$idCompanyZPresenterPerson*/ ) as $job) {
            $jobs[] = array_merge( $job, ['container' => ${TemplateCompilerInterface::VARNAME_CONTAINER} ] ); 
        }
      
    }
    //###################### -------- konec - reprezentant vystavovatele  ----------------------------------




//------------------------------------------------------
/** @var EnrollRepo $enrollRepo */
$enrollRepo = $container->get(EnrollRepo::class);
$enrolls = $enrollRepo->findAll();
$eventCountById = [];
foreach ($enrolls as $enroll) {
    if (array_key_exists($enroll->getEventid(), $eventCountById)) {
        $eventCountById[$enroll->getEventid()]++;
    } else {
        $eventCountById[$enroll->getEventid()] = 1;
    }
}
//--------------------------------------------------------





    //##########------- jen reprezentant vystavovatele (konkretni firmy)  ------------------
    if  ( isset($representativeFromStatus) ) {   
        //##########   
        $idCompany = $representativeFromStatus->getCompanyId(); 
        $companyE = $companyRepo->get($idCompany);
        
        $headline = "Profil vystavovatele";
        $perex = '';
        ?>
        <article class="paper">
            <section>
                <headline>
                    <?php include "headline.php" ?>
                </headline>
                <perex>
                    <?php include "perex.php" ?>
                </perex>
            </section>

            <section>
                <div class="field margin">
                    <label>Společnost</label> 
                    <?= $companyE->getName(); ?>
                    <div class="">
                       <div class="ui styled fluid accordion">


                           <div class="active title">
                                <i class="dropdown icon"></i>
                                Adresa vystavovatele
                           </div>
                           <div class="active content">
                               <?= $this->insert( __DIR__. '/../company-address/company-address.php', $companyAddress ) ?>
                           </div>


                           <div class="active title">
                                <i class="dropdown icon"></i>
                                Kontakty vystavovatele
                           </div>
                           <div class="active content">
                                <?= $this->repeat(__DIR__.'/../company-contacts/company-contact.php', $companyContacts  )  ?>

                                <div class="active title">
                                    <i class="dropdown icon"></i>
                                    Přidejte další kontakt vystavovatele
                                </div>
                                <div class="active content">
                                    <?= $this->insert( __DIR__.'/../company-contacts/company-contact.php', 
                                                                    [ 'companyId' => $idCompany /*$idCompanyZPresenterPerson*/ ] ) ?>
                                </div>
                           </div>
                        </div>
                    </div>
                </div>

                <?php include "content/profil.php" ?> <!-- Tiny pro .edit-userinput -->
            </section>
        </article>
     
    <?php
    //##### --- jen representant vystavovatele (konkretni firmy) - konec ------------------
    
    
    } else {
        $headline = "Profil vystavovatele";
        $perex = "Profil vystavovatele je dostupný pouze přihlášenému representantu.";
        ?>
        <article class="paper">
            <section>
                <headline>
                    <?php include "headline.php" ?>
                </headline>
                <perex>
                    <?php include "perex.php" ?>
                </perex>
            </section>
        </article>
    <?php
    }
    ?>

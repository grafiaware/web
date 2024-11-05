<?php
use Template\Compiler\TemplateCompilerInterface;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;
use Access\Enum\RoleEnum;

use Status\Model\Entity\StatusSecurity;
use Status\Model\Repository\StatusSecurityRepo;

use Events\Model\Repository\EnrollRepo;
use Events\Model\Repository\RepresentativeRepo;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Repository\RepresentativeRepoInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyContactRepoInterface;
use Events\Model\Repository\CompanyAddressRepoInterface;

use Events\Middleware\Events\ViewModel\JobViewModel;
//use Events\Middleware\Events\ViewModel\RepresentativeViewModel;

use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyContactInterface;

use Pes\Text\Html;
use Pes\Text\Text;

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;
use Auth\Model\Entity\LoginAggregateFullInterface;


    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);
    $role = $statusViewModel->getUserRole();
    $representativeFromStatus = $statusViewModel->getRepresentativeActions()->getRepresentative();
    
    //$loginName = $statusViewModel->getUserLoginName();    
    $loginName = isset($representativeFromStatus) ? $representativeFromStatus->getLoginLoginName() : null;
    $idCompany = isset($representativeFromStatus) ? $representativeFromStatus->getCompanyId() : null ; 
    //---------- $idCompany je company prihlaseneho representanta
    
    $isRepresentative = (isset($role) AND $role==RoleEnum::REPRESENTATIVE);





//$readonly = 'readonly="1"';
//$disabled = 'disabled="1"';
if ($editable) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }


//$isRepresentative = false;
//
////######################     natvrdo zvoleny vystavovatel
////$idCompanyVystavovatele = 10; 
//$idCompanyVystavovatele = 25; 
////$idCompanyVystavovatele = 35; 
//// tato stranka musi byt dostupna jen z odkazu na strance firmy (vystavovatele), součástí odkazu pak musí být company name (nebo id)
// //------------------------------------------------------------------
//    /** @var StatusViewModelInterface $statusViewModel */
//    $statusViewModel = $container->get(StatusViewModel::class);
//    $representativeFromStatus = $statusViewModel->getRepresentativeActions()->getRepresentative();
//    $loginName = isset($representativeFromStatus) ? $representativeFromStatus->getLoginLoginName() : null;
//    $idCompany = isset($representativeFromStatus) ? $representativeFromStatus->getCompanyId() : null ; 
//    //---------------------------------------------
//    $isRepresentative = (isset($representativeFromStatus) AND $representativeFromStatus->getCompanyId()==$companyId); //$idCompanyVystavovatele
//
////________________?????????????????????????????????

////######################
//    if ($statusViewModel->isUserLoggedIn() AND $statusViewModel->getUserRole()==ConfigurationCache::auth()['roleRepresentative']) {
//        /** @var RepresentativeViewModel $representativeViewModel */
//        $representativeViewModel = $container->get(RepresentativeViewModel::class );
//        //$representativeViewModel->
//        
//        $isRepresentative =  $representativeViewModel->isRepresentative($loginName, $idCompanyVystavovatele);  // z representative a company  
//    }



//if (isset($loginAggregate)) {
//    $loginName = $loginAggregate->getLoginName();
//    $role = $loginAggregate->getCredentials()->getRoleFk();
//    if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']))  {
//        
//        
//        //$representativeViewMode  NECHCEME
//        
//        /** @var RepresentativeViewModel $representativeViewModel */
//        $representativeViewModel = $container->get(RepresentativeViewModel::class );
//        $companyEntity = $representativeViewModel->getRepresentativesList($idCompanyVystavovatele);
//        $isRepresentative =  $representativeViewModel->isRepresentative($loginName, $idCompanyVystavovatele);  // z representative a company  
//    }
//}


/** @var RepresentativeRepoInterface $representativeRepo */
$representativeRepo = $container->get(RepresentativeRepo::class );
/** @var CompanyRepoInterface $companyRepo */
$companyRepo = $container->get(CompanyRepo::class );
/** @var CompanyContactRepoInterface $companyContactRepo */
$companyContactRepo = $container->get(CompanyContactRepo::class );
/** @var CompanyAddressRepoInterface $companyAddressRepo */
$companyAddressRepo = $container->get(CompanyAddressRepo::class );
//------------------------------------------------------------------        
          
        
        $companyE = $companyRepo->get($idCompany);
 
        
// v job.php je toto   
//        $isRepresentative = (isset($representativeFromStatus) AND $representativeFromStatus->getCompanyId()==$companyId);
////------------------------------------------------------------------------------------------------------------------------

    //###################### ----------------- jen reprezentant vystavovatele (konkretni firmy)  ----------------------------------
    if  ( $isRepresentative ) { 
        
        
//________________
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurity $statusSecurity */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateFullInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();
//___________


/** @var JobViewModel $jobModel */
$jobModel = $container->get( JobViewModel::class );


   
        
        //pro  profil.php
        $representativeContext =  [  //representative a company
            'logNameRepresentative' =>   $loginName,       //$representativeEntity->getLoginLoginName(),
            'idCompany' =>  $companyE->getId(),
            'nameCompany' =>  $companyE->getName(),
            ];            
        $representativeContext ['regmail'] = $loginAggregate->getRegistration()->getEmail();       //BERU Z REGISTRACE doplnen mail                            
//        $companyNameZPresenterPerson = $representativeContext['nameCompany'];                    
//        $idCompanyZPresenterPerson = $representativeContext['idCompany'];

        
        
        //----------------pro company  vypsat vsechny companyContact -----------------
        // $companyContactEntities = $companyContactRepo->find( " company_id = :idCompany ",  ['idCompany' => $idCompanyZPresenterPerson ] );
        $companyContactEntities = $companyContactRepo->find( " company_id = :idCompany ",  ['idCompany' => $idCompany ] );
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
        //-------------- pro tuto company vypsat companyAddress
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

        
        
        //-------------- jobsy pro company tohoto representative
        //TODO: odstranit předávání kontejneru - potřebuje ho vypis-pozic\pozice_2.php   
        foreach ($jobModel->getCompanyJobList($idCompany  /*$idCompanyZPresenterPerson*/ ) as $job) {
            $jobs[] = array_merge( $job, ['container' => ${TemplateCompilerInterface::VARNAME_CONTAINER} /*, 
                                          'nameCompanyZPrezentera' => $companyNameZPresenterPerson */ ] ); 
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







if  ( $isRepresentative ) {
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
                <?=  $companyNameZPresenterPerson ?>
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

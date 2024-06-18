<?php
use Template\Compiler\TemplateCompilerInterface;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

use Status\Model\Repository\StatusSecurityRepo;

use Status\Model\Entity\StatusSecurity;
use Events\Model\Repository\EnrollRepo;
use Events\Model\Repository\RepresentativeRepo;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\CompanyAddressRepo;

use Events\Middleware\Events\ViewModel\JobViewModel;
use Events\Middleware\Events\ViewModel\RepresentativeViewModel;

use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyContactInterface;

use Pes\Text\Html;
use Pes\Text\Text;

use Auth\Model\Entity\LoginAggregateFullInterface;


/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurity $statusSecurity */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateFullInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();

$readonly = 'readonly="1"';
$disabled = 'disabled="1"';

$isRepresentative = false;

//######################     natvrdo zvoleny vystavovatel
$idCompanyVystavovatele = 10;       // tato stranka musi byt dostupna jen z odkazu na strance firmy (vystavovatele), součástí odkazu pak musí být company name (nebo id)
//######################
    
if (isset($loginAggregate)) {
    $loginName = $loginAggregate->getLoginName();
    $role = $loginAggregate->getCredentials()->getRoleFk() ?? '';
    if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']))  {
        /** @var RepresentativeViewModel $representativeViewModel */
        $representativeViewModel = $container->get(RepresentativeViewModel::class );
        $companyEntity = $representativeViewModel->getRepresentativeCompany($representativeEntity);
        $representativeEntity = $representativeViewModel->getRepresentative($loginName, $idCompanyVystavovatele);  // z representative a company
        if (isset($representativeEntity)) {  
            $isRepresentative = true;
        }
    }
}


    /** @var JobViewModel $jobModel */
    $jobModel = $container->get( JobViewModel::class );


    
    /** @var RepresentativeRepo $representativeRepo */
    $representativeRepo = $container->get(RepresentativeRepo::class );
    /** @var CompanyRepo $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var CompanyContactRepo $companyContactRepo */
    $companyContactRepo = $container->get(CompanyContactRepo::class );
    /** @var CompanyAddressRepo $companyAddressRepo */
    $companyAddressRepo = $container->get(CompanyAddressRepo::class );
    //------------------------------------------------------------------
    
        
    
    
    //###################### ----------------- jen reprezentant vystavovatele  ----------------------------------
    if ($isRepresentative) {
        $representativeContext =  [  //representative a company
            'logNameRepresentative' =>  $representativeEntity->getLoginLoginName(),
            'idCompany' =>  $companyEntity->getId(),
            'nameCompany' =>  $companyEntity->getName(),
            'eventInstitutionNameCompany' =>  $companyEntity->getEventInstitutionName30(),                        
            ];            
        $representativeContext ['regmail'] = $loginAggregate->getRegistration()->getEmail(); //BERU Z REGISTRACE doplnen mail                            
        $companyNameZPresenterPerson = $representativeContext['nameCompany'];                    
        $idCompanyZPresenterPerson = $representativeContext['idCompany'];

        //----------------------------pro company z PresenterPerson vypsat vsechny companyContact -----------------
        $companyContactEntities = $companyContactRepo->find( " company_id = :idCompany ",  ['idCompany' => $idCompanyZPresenterPerson ] );
        $companyContacts=[];
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
        //----------------------- pro tuto company vypsat companyAddress
        $companyAddress=[];
        /** @var CompanyAddressInterface $companyAddressEntity */
        $companyAddressEntity = $companyAddressRepo->get( $idCompanyZPresenterPerson );
        if ($companyAddressEntity) {
            $companyAddress = [
                'companyId'=> $companyAddressEntity->getCompanyId(),
                'name'   => $companyAddressEntity->getName(),
                'lokace' => $companyAddressEntity->getLokace(),
                'psc'    => $companyAddressEntity->getPsc(),
                'obec'   => $companyAddressEntity->getObec()
                ];                                                                                                                                                                                                                                                                                                                                                                                                                                      
        }
        else {
            $companyAddress = ['companyId' =>   $idCompanyZPresenterPerson ];
        }

        //-------------- jobsy pro company tohoto representative
        //TODO: odstranit předávání kontejneru - potřebuje ho vypis-pozic\pozice_2.php   
        foreach ($jobModel->getCompanyJobList($idCompanyZPresenterPerson) as $job) {
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


if($isRepresentative) {
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
                                <?= $this->insert( __DIR__.'/../company-contacts/company-contact.php', [ 'companyId' => $idCompanyZPresenterPerson ] ) ?>
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

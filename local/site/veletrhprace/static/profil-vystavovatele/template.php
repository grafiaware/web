<?php
use Template\Compiler\TemplateCompilerInterface;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

use Status\Model\Repository\StatusSecurityRepo;

use Events\Model\Repository\PozadovaneVzdelaniRepo;
use Events\Model\Repository\JobToTagRepo;
use Events\Model\Repository\EnrollRepo;
use Events\Model\Repository\RepresentativeRepo;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\CompanyAddressRepo;

use Events\Model\Arraymodel\JobArrayModel;
use Events\Model\Arraymodel\Presenter;

use Events\Model\Entity\JobToTagInterface;
use Events\Model\Entity\JobInterface as JobEntityInterface;
use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\PozadovaneVzdelaniInterface;
use Events\Model\Entity\RepresentativeInterface;


use Pes\Text\Html;
use Pes\Text\Text;

use Auth\Model\Entity\LoginAggregateFullInterface;


/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurity $statusSecurity */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateFullInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();

if (isset($loginAggregate)) {
    $loginName = $loginAggregate->getLoginName();
    $role = $loginAggregate->getCredentials()->getRole() ?? '';
}
    $readonly = 'readonly="1"';
    $disabled = 'disabled="1"';
//        $readonly = '';
//        $disabled = '';
    $isRepresentative = false;

    /** @var JobArrayModel $jobModel */
    $jobModel = $container->get( JobArrayModel::class );   //ARRAY model
    /** @var Presenter $presenterModel */
    $presenterModel = $container->get(Presenter::class );       //ARRAY model
    
    /** @var RepresentativeRepo $representativeRepo */
    $representativeRepo = $container->get(RepresentativeRepo::class );
    /** @var CompanyRepo $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var CompanyContactRepo $companyContactRepo */
    $companyContactRepo = $container->get(CompanyContactRepo::class );
    /** @var CompanyAddressRepo $companyAddressRepo */
    $companyAddressRepo = $container->get(CompanyAddressRepo::class );
    //------------------------------------------------------------------

    //######################     natvrdo zvoleny vystavovatel
    $idCompanyVystavovatele = 10;       
    //-------------------------------------------------------------------
    
    
    
    //###################### ----------------- jen reprezentant vystavovatele  ----------------------------------
    if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative'])
                    AND  $representativeRepo->get($loginName, $idCompanyVystavovatele) )  {
        $isRepresentative = true;

        //--- Z 'ARRAY' MODELU------------------------
        // každý s rolí presenter musí existovat v modelu (tj. v databázi) jako presenterPerson
        $presenterPerson = $presenterModel->getPerson($loginName, $idCompanyVystavovatele);  // z represrntative a company
        //  vznikne   array 
        //  
        //   z representative - 'regname' - t.. login_login_name
        //   z  company.name - 'regcompany', 'shortName', "name"    //   z  company - "eventInstitutionName", id'
        //   'regmail',
        
        // 'logNameRepresentative' - tj. login_login_name, z representative 
        // 'idCompany', 'nameCompany' , 'eventInstitutionNameCompany' - z  company
//$presenterJobs = array();
        
            //if ($representativePersonI) {
        if ($presenterPerson) {
            $presenterPerson ['regmail'] = $loginAggregate->getRegistration()->getEmail(); //BERU Z REGISTRACE doplnen mail
         
        
        $companyNameZPresenterPerson = $presenterPerson['nameCompany'];  
        //------------------- pro tuto company vypsat vsechny companyContact
         //$idCompanyFromRepresentative = $representativePersonI['idCompany'];
        $idCompanyZPresenterPerson = $presenterPerson['idCompany'];

        //-------------------        
 //TODO: odstranit předávání kontejneru - potřebuje ho vypis-pozic\pozice_2.php   
        foreach ($jobModel->getCompanyJobList($idCompanyZPresenterPerson) as $job) {
           // $jobs[] = array_merge( $job, ['container' => ${TemplateCompilerInterface::VARNAME_CONTAINER}, 'shortName' => $companyNameZPresenterPerson] );
            $jobs[] = array_merge( $job, ['container' => ${TemplateCompilerInterface::VARNAME_CONTAINER}, 'nameCompany' => $companyNameZPresenterPerson] );
 
        }

        //Z DB lze PRECIST REPRESENTATIVE - ale vlastnosti nema zadne, krome id_company
        // $loginName je přihlášený,  zjištěno z $loginAggregate
       // $representativePersonI = $presenterModel->getPerson($loginName, $idCompanyVystavovatele);    // Z DB z  tabulek representative a company
         //  array  'regname',  'regcompany', 'idCompany', 'name', 'eventInstitutionName', 'shortName',   'regmail'

        //$representativeCompany = $companyRepo->get( /*$idCompanyFromRepresentative */ $idCompanyZPresenterPerson);
        
        
        //----------------------------------------------
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
                    'companyId'=> /*$idCompanyFromRepresentative,*/ $idCompanyZPresenterPerson,
                    'companyIdA' => /*$idCompanyFromRepresentative,*/ $idCompanyZPresenterPerson,
                    'name'   => $companyAddressEntity->getName(),
                    'lokace' => $companyAddressEntity->getLokace(),
                    'psc'    => $companyAddressEntity->getPsc(),
                    'obec'   => $companyAddressEntity->getObec()
                    ];                                                                                                                                                                                                                                                                                                                                                                                                                                      
            }
            else {
                $companyAddress = ['companyId' => /*$idCompanyFromRepresentative*/  $idCompanyZPresenterPerson ];
            }

            // jobsy pro company tohoto representative
            $jobs = $jobModel->getCompanyJobList( $idCompanyZPresenterPerson  );

        }// $representativePersonI $presenterPerson

    } //representative AND role





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
                <?= /*$representativeCompany->getName()*/ $companyNameZPresenterPerson ?>
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

            <?php include "content/profil.php" ?> <!-- Tiny pro .working-data -->
        </section>
    </article>
<?php
} else {
    $headline = "Profil vystavovatele";
    $perex = "Profil vystavovatele je dostupný pouze přihlášenému vystavovateli.";
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

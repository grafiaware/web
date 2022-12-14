<?php
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

use Events\Model\Arraymodel\Job;
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
    
    /** @var Job $jobModel */
    $jobModel = $container->get( Job::class );   //ARRAY model
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
    
   
    if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) AND  $representativeRepo->get($loginName) )  {
        $isRepresentative = true;        
        
    //--- Z ARRAY MODELU------------------------
        $presenterPerson = $presenterModel->getPerson($loginName);
        //  vznikne   array 'regname', 'regmail', 'regcompany', 'shortName'  ||  "name", "eventInstitutionName", "shortName"            
        $presenterJobs = array();
        $shortName = $presenterPerson['shortName'];  // každý s rolí presenter musí existovat v modelu jako presenterPerson
        foreach ($jobModel->getCompanyJobList($shortName) as $job) {
            $jobs[] = array_merge($job, ['container' => $container, 'shortName' => $shortName]);
        } // toto $jobs nepouzite, nize jsou data prirazena z db
          //$presenterPerson dale nepouzite     
    //-------------------------------------------    
                
        
        
    //Z DB lze PRECIST REPRESENTATIVE - ale vlastnosti nema zadne, krome id_company             
        $representativePersonI = $presenterModel->getPersonI($loginName);    // Z DB tabulky representative a tabulky company   //z  tabulek representative a company
        if ($representativePersonI) {            
            $representativePersonI ['regmail'] = $loginAggregate->getRegistration()->getEmail(); //BERU Z REGISTRACE doplnen mail 
            //  array  'regname'. 'regmail', 'regcompany', 'idCompany', 'name', 'eventInstitutionName', 'shortName'
            
           //pro tuto company vypsat vsechny companyContact                      
            $idCompanyFromRepresentative = $representativePersonI['idCompany'];
            $representativeCompany = $companyRepo->get($representativePersonI['idCompany']);
            $companyContactObjects = $companyContactRepo->find( " company_id = :idCompany ",  ['idCompany'=> $representativePersonI['idCompany'] ] );
            $companyContacts=[];
            foreach ($companyContactObjects as $cntct) {
                /** @var CompanyContactInterface $cntct */
                $companyContacts[] = [
                    'companyId' => $cntct->getCompanyId(),
                    'name' =>  $cntct->getName(),
                    'phones' =>  $cntct->getPhones(),
                    'mobiles' =>  $cntct->getMobiles(),
                    'emails' =>  $cntct->getEmails()
                    ];
            }
           //-------------------------------
            
            // jobsy pro company tohoto representative
            /** @var PozadovaneVzdelaniRepo $pozadovaneVzdelaniRepo */
            $pozadovaneVzdelaniRepo = $container->get(PozadovaneVzdelaniRepo::class );
            /** @var JobToTagRepo $jobToTagRepo */
            $jobToTagRepo = $container->get(JobToTagRepo::class );  
            $companyJobs = $jobModel->getCompanyJobListI(  $representativePersonI ['idCompany']  );        
            $jobsI = [];
            foreach ($companyJobs as $jobI) {
             /** @var JobEntityInterface  $jobI */
                $jb = [];      
                $jb['jobId'] = $jobI->getId();
                $jb['companyId'] = $jobI->getCompanyId();
                $jb['shortName'] = $representativePersonI['name'];

                $jb['nazev'] = $jobI->getNazev();
                $jb['mistoVykonu'] = $jobI->getMistoVykonu();
                $jb['nabizime'][] = $jobI->getNabizime();
                $jb['popisPozice'] = $jobI->getPopisPozice();            
                /** @var PozadovaneVzdelaniInterface  $pozadovaneVzdelaniEntita */
                $pozadovaneVzdelaniEntita = $pozadovaneVzdelaniRepo->get($jobI->getPozadovaneVzdelaniStupen() );
                $jb['vzdelani']= $pozadovaneVzdelaniEntita->getVzdelani() ;          
                $jb['pozadujeme'][] = $jobI->getPozadujeme();      

                $jTTs = $jobToTagRepo->findByJobId($jobI->getId());
                /** @var JobToTagInterface $jTT */
                foreach ($jTTs as $jTT)  {
                    $jb['kategorie'][] = $jTT->getJobTagTag();
                }
                $jobsI[] = array_merge($jb, ['container' => $container ] ); 
            } //foreach
            
            $jobs = $jobsI;            
            // jobsy pro company tohoto representative        
            //------------------------------------------------------------------
                    
            
        }// $representativePersonI
                        
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
    $headline = "Profil vystavovatele  --*headline";
    $perex = ' **I** ' . $loginAggregate->getLoginName() . '  --*perex';
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
                             
                <?= $representativeCompany->getName() ?>
                
                
                <div class="">
                    <div class="ui styled fluid accordion">   
                                                        
                        <div class="active title">
                             <i class="dropdown icon"></i>
                            Kontakty vystavovatele
                        </div>                        
                        <div class="active content">                                                
                            <?= $this->repeat(__DIR__.'/content/company/company-contact.php', $companyContacts  )  ?>
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

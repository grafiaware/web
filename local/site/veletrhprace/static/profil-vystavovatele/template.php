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
            //doplnit mail
            $representativePersonI ['regmail'] = $loginAggregate->getRegistration()->getEmail(); //BERU Z REGISTRACE
            //  array  'regname'. 'regmail', 'regcompany', 'idCompany', 'name', 'eventInstitutionName', 'shortName'
            
           //pro tutez company vypsat vsechny companyContact
           
//           /** @var RepresentativeInterface $representative */ 
//           $representative = $representativeRepo->findAll($representativePersonI['idCompany'] );          
//           /** @var CompanyInterface $company */ 
//           $companyId = $companyRepo->get($representative->getCompanyId());
//           /** @var CompanyContactInterface $companyContact */ 
            
           $companyContacts = $companyContactRepo->find( " company_id = :idCompany ",  ['idCompany'=>$representativePersonI['idCompany'] ] );
           //  ( " company_id = :idCompany " , [ 'idCompany' => $idCompany ])
           
        ?> 
        <!--   <div class="vypis-prac-pozic">
                <div class="ui styled fluid accordion">        
                    <  ?= $this->repeat(__DIR__.'/vypis-pozic/pozice_2.php', $jobs  )  ?>
                </div>
            </div>  -->  
            
            
         <?php
            
            
            // jobsy pro company tohoto representative
            /** @var PozadovaneVzdelaniRepo $pozadovaneVzdelaniRepo */
            $pozadovaneVzdelaniRepo = $container->get(PozadovaneVzdelaniRepo::class );
            /** @var JobToTagRepo $jobToTagRepo */
            $jobToTagRepo = $container->get(JobToTagRepo::class );  
            $companyJobs = $jobModel->getCompanyJobListI(  $representativePersonI ['idCompany'] /*$companyEntity->getId()*/ );        
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
            
            
            
            
            
            
            
            
        }
                        
    }
        
 
    
    
    
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
    $perex = ' **I** ' . $loginAggregate->getLoginName();
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
                <select <?= $disabled ?>>
                    <?php
                    foreach ($presenterModel->getCompanyList() as $shortN => $comp) {
                    ?>
                        <option value="<?= $comp['name']?>" <?= $shortN==$representativePersonI['shortName'] ? "selected" : "" ?> > <?= $comp['name']?></option>
                    <?php
                    }
                    ?>
                </select>
                
                **I**
                <select <?= $disabled ?>>
                    <?php
                    /** @var CompanyInterface $compI */
                    foreach ($presenterModel->getCompanyListI() as $shortN => $compI) {
                    ?>
                    <option value="<?= $compI->getName() ?>" <?= $shortN==$representativePersonI['shortName'] ? "selected" : "" ?> > <?= $compI->getName() ?></option>
                    <?php
                    }
                    ?>
                </select>
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

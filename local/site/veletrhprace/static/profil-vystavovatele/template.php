<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

use Status\Model\Repository\StatusSecurityRepo;

use Events\Model\Repository\PozadovaneVzdelaniRepo;
use Events\Model\Repository\JobToTagRepo;
use Events\Model\Repository\EnrollRepo;
use Events\Model\Arraymodel\Job;
use Events\Model\Arraymodel\Presenter;
use Events\Model\Entity\JobToTag;
use Events\Model\Entity\Job as JobEntity;

use Pes\Text\Html;
use Pes\Text\Text;

use Auth\Model\Entity\LoginAggregateFullInterface;

//use Events\Model\Repository\VisitorProfileRepo;
//use Events\Model\Entity\VisitorProfileInterface;
//use Events\Model\Repository\DocumentRepo;
//use Events\Model\Entity\DocumentInterface;

//------------------------------------------
//$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
///** @var StatusSecurityRepo $statusSecurityRepo */
//$statusSecurity = $statusSecurityRepo->get();
///** @var LoginAggregateCredentialsInterface $loginAggregate */
//$loginAggregate = $statusSecurity->getLoginAggregate();
//if (isset($loginAggregate)) {
//    $credentials = $loginAggregate->getCredentials();
//    $role = $credentials->getRole(); 
//if (isset($loginAggregate)) {
//    $credentials = $loginAggregate->getCredentials();
//    $role = $creden tials->getRole();    
//    $loginName = $loginAggregate->getLoginName();
//------------------------------------------




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
           
    $isPresenter = false;                 
    /** @var Job $jobModel */
    $jobModel = $container->get( Job::class );
    /** @var Presenter $presenterModel */
    $presenterModel = $container->get(Presenter::class );       
        
    if(isset($role) AND $role==ConfigurationCache::loginLogoutController()['roleRepresentative']) {
        $isPresenter = true;
        
    // Z ARRAY MODELU
        $presenterPerson = $presenterModel->getPerson($loginName);
        //  vznikne   array 'regname', 'regmail', 'regcompany', 'shortName'  ||  "name", "eventInstitutionName", "shortName"    
        
        $presenterJobs = array();
        $shortName = $presenterPerson['shortName'];  // každý s rolí presenter musí existovat v modelu jako presenterPerson
        foreach ($jobModel->getCompanyJobList($shortName) as $job) {
            $jobs[] = array_merge($job, ['container' => $container, 'shortName' => $shortName]);
        }
   
        
        
    //Z DB lze PRECIST REPRESENTATIVE - ale vlastnosti nema zadne, krome id_company    
        $presenterPersonI = $presenterModel->getPersonI($loginName);
        if ($presenterPersonI) {
            $presenterPersonI ['regmail'] = $loginAggregate->getRegistration()->getEmail(); 
            //  array  'regname'. 'regmail', 'regcompany', 'idCompany', 'name', 'eventInstitutionName', 'shortName'
        
            /** @var PozadovaneVzdelaniRepo $pozadovaneVzdelaniRepo */
            $pozadovaneVzdelaniRepo = $container->get(PozadovaneVzdelaniRepo::class );
            /** @var JobToTagRepo $jobToTagRepo */
            $jobToTagRepo = $container->get(JobToTagRepo::class );  
            $companyJobs = $jobModel->getCompanyJobListI(  $presenterPersonI ['idCompany'] /*$companyEntity->getId()*/ );        
            $jobsI = [];
            foreach ($companyJobs as $jobI) {
             /** @var JobEntity  $jobI */
                $jb = [];      
                $jb['jobId'] = $jobI->getId();
                $jb['companyId'] = $jobI->getCompanyId();
                $jb['shortName'] = $companyEntity->getName();

                $jb['nazev'] = $jobI->getNazev();
                $jb['mistoVykonu'] = $jobI->getMistoVykonu();
                $jb['nabizime'][] = $jobI->getNabizime();
                $jb['popisPozice'] = $jobI->getPopisPozice();            
                /** @var PozadovaneVzdelani  $pozadovaneVzdelaniEntita */
                $pozadovaneVzdelaniEntita = $pozadovaneVzdelaniRepo->get($jobI->getPozadovaneVzdelaniStupen() );
                $jb['vzdelani']= $pozadovaneVzdelaniEntita->getVzdelani() ;          
                $jb['pozadujeme'][] = $jobI->getPozadujeme();      

                $jTTs = $jobToTagRepo->findByJobId($jobI->getId());
                /** @var JobToTag  $jTT */
                foreach ($jTTs as $jTT)  {
                    $jb['kategorie'][] = $jTT->getJobTagTag();
                }
                $jobsI[] = array_merge($jb, ['container' => $container ] ); 
            } //foreach
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





if($isPresenter) {
    $headline = "Profil vystavovatele";
    $perex = $loginAggregate->getLoginName();
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
                        <option value="<?= $comp['name']?>" <?= $shortN==$presenterPerson['shortName'] ? "selected" : "" ?> > <?= $comp['name']?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
                <?php include "content/profil.php" ?> <!-- Tiny pro .working-data -->
        </section>
    </article>
    <?php
} else {    ?>
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

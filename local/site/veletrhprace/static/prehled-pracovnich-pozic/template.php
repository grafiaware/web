<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

use Events\Model\Arraymodel\Job;
use Events\Model\Arraymodel\Presenter;
use Red\Model\Repository\BlockRepo;

use Events\Model\Repository\PozadovaneVzdelaniRepo;
use Events\Model\Repository\JobToTagRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\Company;
use Events\Model\Entity\Job as JobEntity;
use Events\Model\Entity\JobToTag;
use Events\Model\Entity\PozadovaneVzdelani;

$headline = 'Pracovní pozice';
$perex = 'Vítejte prehled-pracovnich-pozic ';



/** @var Presenter $presenterModel */
$presenterModel = $container->get( Presenter::class );
/** @var Job $jobModel */
$jobModel = $container->get( Job::class );



// odkaz na stánek - v tabulce blok musí existovat položka s názvem==$shortName
/** @var BlockRepo $blockRepo */
// SVOBODA - čeká ba Red databázi - slouží pro generování odkazů na stránku firmy
//
//$blockRepo = $container->get(BlockRepo::class);

foreach ($jobModel->getShortNamesList() as $shortName) {
// SVOBODA - čeká ba Red databázi - slouží pro generování odkazů na stránku firmy
//    
//    $block = $blockRepo->get($shortName);
    $presenterJobs = $jobModel->getCompanyJobList($shortName);
    $jobs = [];
    foreach ($presenterJobs as $job) {
        $jobs[] = array_merge($job, ['container' => $container, 'shortName' => $shortName /*, 'block' => $block*/ ] );  // přidání $container a $shortName pro template pozice
    }
    $allJobs[] = [
                'shortName' => $shortName,
                'presenterName' => $presenterModel->getCompany($shortName)['name'],
                //'block' => $block,
                'presenterJobs' => ['jobs' => $jobs],
                'container' => $container
            ];
}



//--------------------------------------------------------------- CIST z DB ----
    /** @var PozadovaneVzdelaniRepo $pozadovaneVzdelaniRepo */
    $pozadovaneVzdelaniRepo = $container->get(PozadovaneVzdelaniRepo::class );
    /** @var JobToTagRepo $jobToTagRepo */
    $jobToTagRepo = $container->get(JobToTagRepo::class );
    
    /** @var CompanyContactRepo $companyContactRepo */
    $companyContactRepo = $container->get(CompanyContactRepo::class);
    


    $companyListArray = $presenterModel->getCompanyListI(); 
    foreach ($companyListArray as $companyEntity ) {       
        
//        $companyContactEntities = $companyContactRepo->find( ' company_id = :companyId ',  [  'companyId'  =>$companyEntity->getId() ] ) ;
//        $сompanyEmailsA = '';
//        foreach ($companyContactEntities  as  $companyContactEntity) {
//            $сompanyEmails  = '';
//            $e = $companyContactEntity->getEmails();
//            $сompanyEmails  = ( $e  ) ?   $e.';'  : '' ;
//            $сompanyEmailsA =  $сompanyEmailsA . $сompanyEmails; 
//        }
           
        
        $companyJobs = $jobModel->getCompanyJobListI($companyEntity->getId());        
        $jobsI = [];
        foreach ($companyJobs as $jobI) {
         /** @var JobEntity  $jobI */
            $jb = [];      
            $jb['jobId'] = $jobI->getId();
            $jb['companyId'] = $jobI->getCompanyId();
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
            $jobsI[] = array_merge($jb, ['container' => $container, /*, 'block' => $block*/ ] ); 

        }
        
        /** @var Company $companyEntity */
        $allJobsI[] = [
                'companyId' => $companyEntity->getId(),
                'shortName' => $companyEntity->getName(),
                'presenterName' => $companyEntity->getName(),
                //'block' => $block,
                'presenterJobs' => ['jobs' => $jobsI],
                'container' => $container                               
                ];
    }
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
        <content class='prehled-pozic'>
            xxxxxxxxxxxxxx template ***
            <?=  $this->repeat(__DIR__.'/content/presenter-jobs.php', $allJobsI);  ?>

        </content>
    </section>
</article>


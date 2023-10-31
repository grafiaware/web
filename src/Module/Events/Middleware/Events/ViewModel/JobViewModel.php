<?php
namespace Events\Middleware\Events\ViewModel;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\JobToTagRepoInterface;
use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Repository\PozadovaneVzdelaniRepoInterface;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\PozadovaneVzdelaniInterface;
use Events\Model\Entity\JobInterface;
use Events\Model\Entity\JobToTagInterface;
use Events\Model\Entity\JobTagInterface;
 
use Template\Compiler\TemplateCompilerInterface;


class JobViewModel {

    /**
     * @var CompanyRepoInterface
     */
    private $companyRepo;
    
    /**
     * @var JobRepoInterface
     */
    private $jobRepo;

    /**
     * @var JobToTagRepoInterface
     */
    private $jobToTagRepo;

    /**
     * @var JobTagRepoInterface
     */
    private $jobTagRepo;

    /**
     * @var PozadovaneVzdelaniRepoInterface
     */
    private $pozadovaneVzdelaniRepo;

    
    public function __construct(
            CompanyRepoInterface $companyRepo,
            JobRepoInterface $jobRepo,
            JobToTagRepoInterface $jobToTagRepo,
            JobTagRepoInterface $jobTagRepo,
            PozadovaneVzdelaniRepoInterface $pozadovaneVzdelaniRepo
        ) {
        $this->companyRepo = $companyRepo;
        $this->jobRepo = $jobRepo;
        $this->jobToTagRepo = $jobToTagRepo;
        $this->jobTagRepo = $jobTagRepo;
        $this->pozadovaneVzdelaniRepo = $pozadovaneVzdelaniRepo;
    }

    /**
     * 
     * @param type $name
     * @return CompanyInterface|null
     */
    public function getCompanyByName($name): ?CompanyInterface {
        return $this->companyRepo->getByName($name);
    }
    
    /**
     * 
     * @param type $idCompany
     * @return array
     */
    public function getCompanyJobList($idCompany): array {
        $companyJobs = $this->jobRepo->find( " company_id = :idCompany " , [ 'idCompany' => $idCompany ]);
        $jobsArray = [];
        foreach ($companyJobs as $job) {
            /** @var JobInterface  $job */
            $jobArray = [];
            $jobArray['jobId'] = $job->getId();
            $jobArray['companyId'] = $job->getCompanyId();

            $jobArray['nazevPozice'] = $job->getNazev();
            $jobArray['mistoVykonu'] = $job->getMistoVykonu();
            $jobArray['nabizime'][] = $job->getNabizime();
            $jobArray['popisPozice'] = $job->getPopisPozice();
            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get($job->getPozadovaneVzdelaniStupen() );
            $jobArray['vzdelani']= $pozadovaneVzdelani->getVzdelani() ;
            $jobArray['pozadujeme'][] = $job->getPozadujeme();

            $jobToTags = $this->jobToTagRepo->findByJobId($job->getId());
            /** @var JobToTagInterface $jobToTag */
            $jobArray['jobTags'] = [];  //pole vždy existuje
            foreach ($jobToTags as $jobToTag)  {
                 $id = $jobToTag->getJobTagId();
                 $tag = $this->jobTagRepo->get($id);
                 $jobArray['jobTags'][] = $tag->getTag();
            }
            $jobsArray[] = array_merge($jobArray);
        }
        return $jobsArray;
    }
}

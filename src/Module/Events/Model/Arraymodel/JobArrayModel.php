<?php
namespace Events\Model\Arraymodel;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\JobToTagRepoInterface;
use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Repository\PozadovaneVzdelaniRepoInterface;

use Template\Compiler\TemplateCompilerInterface;

class JobArrayModel {

    /**
     * @var CompanyRepoInterface
     */
    private $companyRepo;

    /**
     * @var JobRepoInterface
     */
    private $jobRepo;

    /**
     *
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
//            CompanyRepoInterface $companyRepo,
            JobRepoInterface $jobRepo,
            JobToTagRepoInterface $jobToTagRepo,
//            JobTagRepoInterface $jobTagRepo,
            PozadovaneVzdelaniRepoInterface $pozadovaneVzdelaniRepo
        ) {
//        $this->companyRepo = $companyRepo;
        $this->jobRepo = $jobRepo;
        $this->jobToTagRepo = $jobToTagRepo;
//        $this->jobTagRepo = $jobTagRepo;
        $this->pozadovaneVzdelaniRepo = $pozadovaneVzdelaniRepo;
    }

    public function getCompanyJobList($idCompany)  {
        $companyJobs = $this->jobRepo->find( " company_id = :idCompany " , [ 'idCompany' => $idCompany ]);
        $jobsArray = [];
        foreach ($companyJobs as $job) {
            /** @var JobInterface  $job */
            $jobArray = [];
            $jobArray['jobId'] = $job->getId();
            $jobArray['companyId'] = $job->getCompanyId();

            $jobArray['nazev'] = $job->getNazev();
            $jobArray['mistoVykonu'] = $job->getMistoVykonu();
            $jobArray['nabizime'][] = $job->getNabizime();
            $jobArray['popisPozice'] = $job->getPopisPozice();
            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get($job->getPozadovaneVzdelaniStupen() );
            $jobArray['vzdelani']= $pozadovaneVzdelani->getVzdelani() ;
            $jobArray['pozadujeme'][] = $job->getPozadujeme();

            $jobToTags = $this->jobToTagRepo->findByJobId($job->getId());
            /** @var JobToTagInterface $jobToTag */
            foreach ($jobToTags as $jobToTag)  {
                $jobArray['kategorie'][] = $jobToTag->getJobTagTag();
            }
//            $jobsArray[] = array_merge($jobArray, ['container' => ${TemplateCompilerInterface::VARNAME_CONTAINER}] );
            $jobsArray[] = array_merge($jobArray);
        }
        return $jobsArray;
    }
}

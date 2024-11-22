<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Entity\CompanyInterface;

use Events\Model\Repository\JobToTagRepo;
use Events\Model\Repository\JobTagRepo;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\JobToTagRepoInterface;
use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Repository\JobRepoInterface;

use Events\Model\Entity\JobToTag;
use Events\Model\Entity\JobToTagInterface;
use Events\Model\Entity\JobTag;
use Events\Model\Entity\JobTagInterface;
use Events\Model\Entity\Job;
use Events\Model\Entity\JobInterface;


use Component\ViewModel\ViewModelInterface;


use ArrayIterator;

/**
 * 
 */
class JobToTagViewModel extends ViewModelAbstract implements ViewModelInterface {

    private $status;       
    private $jobRepo;
    private $jobToTagRepo;
    private $jobTagRepo;
    private $companyRepo;

    public function __construct(
            StatusViewModelInterface $status,
            JobRepoInterface $jobRepo,
            JobToTagRepoInterface $jobToTagRepo,
            JobTagRepoInterface $jobTagRepo,
            CompanyRepoInterface $companyRepo
            ) {
        $this->status = $status;
        $this->jobRepo = $jobRepo;
        $this->jobToTagRepo = $jobToTagRepo;
        $this->jobTagRepo = $jobTagRepo;
        $this->companyRepo = $companyRepo;
    }
    
    
    public function getIterator() {                        
        // $editable = true;                            
        $requestedId = $this->getRequestedId(); //id jobu                  
        /** @var JobInterface $jobEntity */ 
        $jobEntity = $this->jobRepo->get($requestedId);   
        $jobCompanyId = $jobEntity->getCompanyId();        
        /** @var CompanyInterface $company */
        $company = $this->companyRepo->get($jobCompanyId);
        
        $representativeFromStatus = $this->status->getRepresentativeActions()->getRepresentative();  
        
        $jobToTagies=[];
        if (isset($representativeFromStatus)) {
            $representativeCompanyId = $representativeFromStatus->getCompanyId();        

            $editable = isset($representativeFromStatus) ? ($representativeCompanyId == $jobCompanyId) : false;                            
            //---------------------------                                        
           
                    
            $allTags=[];
            $jobTagEntitiesAll = $this->jobTagRepo->findAll();
            /** @var JobTagInterface  $jobTagEntity */
            foreach ( $jobTagEntitiesAll as $jobTagEntity) {
                $allTags[$jobTagEntity->getTag()] = ["data[{$jobTagEntity->getTag()}]" => $jobTagEntity->getId()] ;
                //$allTagsStrings[ $jobTagEntity->getId() ] = $jobTagEntity->getTag();
            }

            $jobToTagEntities_proJob = $this->jobToTagRepo->findByJobId( $jobEntity->getId() );

            $checkedTags=[];   //nalepky pro 1 job
            $checkedTagsText=[]; 
            foreach ($jobToTagEntities_proJob as $jobToTagEntity) {
               /** @var JobToTagInterface $jobToTagEntity */
              $idDoTag = $jobToTagEntity->getJobTagId();
               /** @var JobTagInterface $tagE */
              $tagE = $this->jobTagRepo->get($idDoTag);
              $checkedTags["data[{$tagE->getTag()}]"] = $tagE->getId()  ;
              $checkedTagsText["{$tagE->getTag()}"] = $tagE->getId()  ;
            }
            $jobToTagies[] = [
                    'jobId'    => $jobEntity->getId(),
                    'jobNazev' => $jobEntity->getNazev(),
                    'allTags'  => $allTags,
                    'checkedTags' => $checkedTags,

                    'checkedTagsText' => $checkedTagsText,
                    'editable' => $editable
            ];
                  
        }
        
        $array = [
                'jobToTagies' => $jobToTagies,
                'companyName' => $company->getName()
                 ];
        return new ArrayIterator($array);        
    }

}

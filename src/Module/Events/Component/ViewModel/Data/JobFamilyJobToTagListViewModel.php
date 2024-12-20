<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyListAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\JobToTagRepoInterface;
use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Repository\CompanyRepoInterface;

use Events\Model\Entity\JobInterface;
use Events\Model\Entity\JobTagInterface;
use Events\Model\Entity\CompanyInterface;

use Events\Component\Model\Entity\JobAggregateTags;

use Access\Enum\RoleEnum;
use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class JobFamilyJobToTagListViewModel extends ViewModelFamilyListAbstract {
    

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

    public function isListEditable(): bool {
        $this->loadCompanyAddress();
        return $this->isAdministrator() || $this->isCompanyEditor($this->companyAddress->getCompanyId());
    }
    
    use RepresentativeTrait;
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyEditor($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }
    
    public function provideItemDataCollection(): iterable {
        $job = $this->jobRepo->get($this->familyRouteSegment->getParentId());
        $jobTagEntitiesAll = $this->jobTagRepo->findAll();
        $jobToTagEntities_proJob = $this->jobToTagRepo->findByJobId($job->getId());
        
        
        
        
        $componentRouteSegment = "events/v1/".$this->getFamilyRouteSegment();  // exception
        
        // $editable = true;                            
        $jobId = $this->getParentId(); //id jobu                  
        /** @var JobInterface $job */ 
        $job = $this->jobRepo->get($jobId);
    
        $editableItem = $this->isAdministrator() || $this->isCompanyEditor($job->getCompanyId());

        $allTags=[];
        $jobTagEntitiesAll = $this->jobTagRepo->findAll();
        /** @var JobTagInterface  $jobTagEntity */
        foreach ( $jobTagEntitiesAll as $jobTagEntity) {
            $allTags[$jobTagEntity->getTag()] = ["data[{$jobTagEntity->getTag()}]" => $jobTagEntity->getId()] ;
        }

        $jobToTagEntities_proJob = $this->jobToTagRepo->findByJobId( $job->getId() );

        $checkedTags=[];   //nalepky pro 1 job
        $checkedTagsText=[]; 
        $jobToTagies=[];
        foreach ($jobToTagEntities_proJob as $jobToTagEntity) {
            /** @var JobToTagInterface $jobToTagEntity */
            $idDoTag = $jobToTagEntity->getJobTagId();
            /** @var JobTagInterface $tagE */
            $tagE = $this->jobTagRepo->get($idDoTag);
            $checkedTags["data[{$tagE->getTag()}]"] = $tagE->getId()  ;
            $checkedTagsText["{$tagE->getTag()}"] = $tagE->getId()  ;

        }
        $jobToTagies[] = [
            // conditions
            'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
            //route
            'componentRouteSegment' => $componentRouteSegment,    // pracuje jen s kolekcíc -> nejsou routy s id jednotlivých job to tag
            // data
            'fields' => [ 
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                'jobId'    => $job->getId(),
                'jobNazev' => $job->getNazev(),
                'allTags'  => $allTags,
                'checkedTags' => $checkedTags,

                'checkedTagsText' => $checkedTagsText,                   ],
        ];        
        return $jobToTagies;
    }
    public function getIterator() {

        $array = [         
            'listHeadline'=>'Tagy', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();        
    }
}
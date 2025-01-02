<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyMultiAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\JobToTagRepoInterface;
use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Repository\CompanyRepoInterface;

use Events\Model\Entity\JobInterface;
use Events\Model\Entity\JobTagInterface;
use Events\Model\Entity\JobToTag;

use Events\Component\Model\Entity\JobAggregateTags;
use Component\ViewModel\FamilyInterface;
use Access\Enum\RoleEnum;
use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class JobFamilyTagMultiViewModel extends ViewModelFamilyMultiAbstract {
    

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
    
    use RepresentativeTrait;
    
    public function isMultiEditable(): bool {
        $job = $this->jobRepo->get($this->getFamilyRouteSegment()->getParentId());
        return $this->isAdministrator() || $this->isCompanyEditor($job->getCompanyId());
    }
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }
        
    protected function newMultiEntity() {
        return new JobToTag();  // "prázdná" entita pro formulář pro přidání
    }

    protected function cardinality() {
        return FamilyInterface::CARDINALITY_0_N;
    }    

    private function getCheckedTags() {
        $job = $this->jobRepo->get($this->getFamilyRouteSegment()->getParentId());
        $jobToTags = $this->jobToTagRepo->findByJobId($job->getId());
        $tags = [];
        foreach ($jobToTags as $jobToTag) {
            $tags[] = $this->jobTagRepo->get($jobToTag->getJobTagId());
        }        
        return $tags;
    }
    
    protected function loadMultiEntitiesMap() {
        // pro renderování tagů - pro editaci potřebujii všechny tagy, pro needitovatelnou verzi jen tagy jobu (checked)
        if (!$this->multiEntitiesMap) {
            if ($this->isMultiEditable()) {
                $tags = $this->jobTagRepo->findAll();
                $this->selectedEntities = $this->getCheckedTags();
            } else {
                $tags = $this->getCheckedTags();
            }
            $this->multiEntitiesMap = [];
            foreach ($tags as $tag) {
                $this->multiEntitiesMap[$tag->getId()] = $tag;
            }
        }
    }
    
//    public function provideItemEntityMapQQ(): iterable {
//    
//        ###########################
//        $job = $this->jobRepo->get($this->getFamilyRouteSegment()->getParentId());
//        $jobTagEntitiesAll = $this->jobTagRepo->findAll();
//        $jobToTagEntities_proJob = $this->jobToTagRepo->findByJobId($job->getId());
//        
//        $jobAgg = new JobAggregateTags();
//        $jobAgg->setNazev($job->getNazev());
//        
//        
//        $componentRouteSegment = "events/v1/".$this->getFamilyRouteSegment()->getPath();  // exception
//        
//        // $editable = true;                            
//        $jobId = $this->getParentId(); //id jobu                  
//        /** @var JobInterface $job */ 
//        $job = $this->jobRepo->get($jobId);
//    
//        $editableItem = $this->isAdministrator() || $this->isCompanyEditor($job->getCompanyId());
//
//        $allTags=[];
//        $jobTagEntitiesAll = $this->jobTagRepo->findAll();
//        /** @var JobTagInterface  $jobTagEntity */
//        foreach ( $jobTagEntitiesAll as $jobTagEntity) {
//            $allTags[$jobTagEntity->getTag()] = ["data[{$jobTagEntity->getTag()}]" => $jobTagEntity->getId()] ;
//        }
//
//        $jobToTagEntities_proJob = $this->jobToTagRepo->findByJobId( $job->getId() );
//
//        $checkedTags=[];   //nalepky pro 1 job
//        $checkedTagsText=[]; 
//        $jobToTagies=[];
//        foreach ($jobToTagEntities_proJob as $jobToTagEntity) {
//            /** @var JobToTagInterface $jobToTagEntity */
//            $idDoTag = $jobToTagEntity->getJobTagId();
//            /** @var JobTagInterface $tagE */
//            $tagE = $this->jobTagRepo->get($idDoTag);
//            $checkedTags["data[{$tagE->getTag()}]"] = $tagE->getId()  ;
//            $checkedTagsText["{$tagE->getTag()}"] = $tagE->getId()  ;
//
//        }
//        $jobToTagies[] = [
//            // conditions
//            'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
//            //route
//            'componentRouteSegment' => $componentRouteSegment,    // pracuje jen s kolekcíc -> nejsou routy s id jednotlivých job to tag
//            // data
//            'fields' => [ 
//                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
//                'jobId'    => $job->getId(),
//                'jobNazev' => $job->getNazev(),
//                'allTags'  => $allTags,
//                'checkedTags' => $checkedTags,
//
//                'checkedTagsText' => $checkedTagsText,                   ],
//        ];        
//        return $jobToTagies;
//    }
    
    private function multiTemplateData($items) {
        $allTags=[];
        // map jsou tagy indexované podle id tagů (se stejnou map byly renderovány items)
        $map = $this->multiEntitiesMap;
        /** @var JobTagInterface  $jobTag */
        foreach ( $map as $id => $jobTag) {
            $label = $items[$id];  //$items jsou remderované tagy indexované podle id tagů
            $allTags[$label] = ["data[{$jobTag->getTag()}]" => $jobTag->getId()] ;
        }        

        $checkedTags=[];   //nalepky pro 1 job
        foreach ($this->selectedEntities as $tag) {
            /** @var JobToTagInterface $jobToTag */
            /** @var JobTagInterface $tag */
//            $tag = $jobTagsById[$jobToTag->getJobTagId()];
            $checkedTags["data[{$tag->getTag()}]"] = $tag->getId();
        }
        return [
            'id' => $this->getFamilyRouteSegment()->getParentId(),
            'allCheckboxes'=>$allTags,
            'checkedCheckboxes'=>$checkedTags
        ];
    }
    
    /**
     * 
     * @return ArrayIterator
     */
    public function getIterator() {
        $componentRouteSegment = $this->getFamilyRouteSegment();
        if ($this->isMultiEditable()) {
            $array = array_merge(
                [         
                'listHeadline'=>'Tagy pracovní pozice',           
                //route
                'actionSave' => $componentRouteSegment->getAddPath(),    // pracuje jen s kolekcí -> nejsou routy s id jednotlivých job to tag
                ],            
                $this->multiTemplateData($this->getArrayCopy())
            );        
        } else {
            $array = [
            'items' => $this->getArrayCopy()
            ];
        }        

        $this->appendData($array);
        return parent::getIterator();        
    }
}
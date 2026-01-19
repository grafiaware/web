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
        return $this->isCompanyEditor($job->getCompanyId());
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
    public function getIterator(): ArrayIterator {
        if ($this->isMultiEditable()) {
            $componentRouteSegment = $this->getFamilyRouteSegment();
            $array = array_merge(
                [         
                'listHeadline'=>'Tagy pracovní pozice',           
                //route
                'actionSave' => $componentRouteSegment->getAddPath(),    // pracuje jen s kolekcí -> nejsou routy s id jednotlivých job to tag
                ]
                , $this->multiTemplateData($this->getArrayCopy())          
            );        
        } else {
            $array = [
            'items'=>$this->getArrayCopy()
            ];
        }        

        $this->appendData($array);
        return parent::getIterator();        
    }
}
<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyListAbstract;
use Component\ViewModel\FamilyInterface;


use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\VisitorJobRequestRepoInterface;
use Events\Model\Entity\VisitorJobRequestInterface;
use Events\Model\Entity\VisitorJobRequest;

use Access\Enum\RoleEnum;
use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class JobFamilyJobRequestListViewModel extends ViewModelFamilyListAbstract {
    
    private $status;
    private $jobRequestRepo;
    
    public function __construct(
            StatusViewModelInterface $status,
            VisitorJobRequestRepoInterface $jobRequestRepo            
            ) {
        $this->status = $status;
        $this->jobRequestRepo = $jobRequestRepo;
    }

    private function isAdministrator() {
        return ($this->status->getUserRole() == RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isVisitor() {
        return ($this->status->getUserRole() == RoleEnum::VISITOR);
    }
    
    private function isJobRequestEditor($jobRequestLoginName) {
        return ($this->status->getUserRole() == RoleEnum::VISITOR AND $this->status->getUserLoginName() == $jobRequestLoginName );
    }
    
    public function isListEditable(): bool {
        return $this->isJobRequestEditor($this->getFamilyRouteSegment()->getChildId());   // visitor má právo přidat request
    }
        
    protected function newListEntity() {
        return new VisitorJobRequest();  // "prázdná" entita pro formulář pro přidání
    }

    protected function cardinality() {
        return FamilyInterface::CARDINALITY_0_N;
    }    

    protected function loadListEntities() {
        if (!$this->listEntities) {
            $this->listEntities = $this->jobRequestRepo->find( "job_id = :jobId ",  ['jobId'=> $this->getFamilyRouteSegment()->getParentId()] );
        }
    }

    /**
     * 
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator {

        $array = [         
            'listHeadline'=>'Zájemci o pozici', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();        
    }
}
<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyListAbstract;
use Component\ViewModel\FamilyInterface;


use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\VisitorJobRequestRepoInterface;
use Events\Model\Repository\RepresentativeRepoInterface;
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
    /** @var JobRepoInterface */
    private $jobRepo;
    /** @var RepresentativeRepoInterface */
    private $representativeRepo;

    use JobRequestAccessTrait;   // #171: kdo smí vidět zájmy o pozici

    public function __construct(
            StatusViewModelInterface $status,
            VisitorJobRequestRepoInterface $jobRequestRepo,
            JobRepoInterface $jobRepo,
            RepresentativeRepoInterface $representativeRepo
            ) {
        $this->status = $status;
        $this->jobRequestRepo = $jobRequestRepo;
        $this->jobRepo = $jobRepo;
        $this->representativeRepo = $representativeRepo;
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
            $jobId = $this->getFamilyRouteSegment()->getParentId();
            $loginName = $this->status->getUserLoginName();
            if ($this->canSeeAllJobRequests($jobId)) {
                // #171: všechny zájmy o pozici jen zástupce firmy pozice a administrátor
                $this->listEntities = $this->jobRequestRepo->find( "job_id = :jobId ",  ['jobId'=> $jobId] );
            } elseif ($this->isVisitor() && isset($loginName)) {
                // návštěvník jen svůj zájem
                $this->listEntities = $this->jobRequestRepo->find( "job_id = :jobId AND login_login_name = :loginName ",  ['jobId'=> $jobId, 'loginName' => $loginName] );
            } else {
                $this->listEntities = [];
            }
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
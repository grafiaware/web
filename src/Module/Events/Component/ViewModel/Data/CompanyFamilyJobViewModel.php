<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyItemAbstract;

use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\PozadovaneVzdelaniRepoInterface;
use Events\Model\Entity\JobInterface;
use Access\Enum\RoleEnum;
use Model\Entity\EntityInterface;

use UnexpectedValueException;

/**
 * 
 */
class CompanyFamilyJobViewModel extends ViewModelFamilyItemAbstract {
    
    private $status;
    private $companyRepo;
    private $jobRepo;
    private $pozadovaneVzdelaniRepo;
    
    /**
     * 
     * @var JobInterface
     */
    private $job;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            JobRepoInterface $jobRepo,
            PozadovaneVzdelaniRepoInterface $pozadovaneVzdelaniRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->jobRepo = $jobRepo;
        $this->pozadovaneVzdelaniRepo = $pozadovaneVzdelaniRepo;    
    }    
    
    use RepresentativeTrait;    

    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof JobInterface) {
            $this->job = $entity;
            $this->getFamilyRouteSegment()->setChildId($this->job->getId());            
        } else {
            $cls = JobInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isDataEditable(): bool {
        return $this->isCompanyEditor($this->getFamilyRouteSegment()->getParentId());
    }
    
    private function selectEducations() {
        $selectEducations = [];
        $selectEducations [''] =  "vyber - povinné pole" ;
        $vzdelaniEntities = $this->pozadovaneVzdelaniRepo->findAll();
        /** @var PozadovaneVzdelaniInterface $vzdelaniEntity */ 
        foreach ( $vzdelaniEntities as $vzdelaniEntity ) {
            $selectEducations [$vzdelaniEntity->getStupen()] =  $vzdelaniEntity->getVzdelani() ;
        }           
        return $selectEducations;
    }
    
    // pokud je volán jako item = !isset($this->job) - načte a zobrazí i nepublikované joby
    private function loadJob() {
        if (!isset($this->job)) {
            if ($this->getFamilyRouteSegment()->hasChildId()) {
                $this->job = $this->jobRepo->get($this->getFamilyRouteSegment()->getChildId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator() {  
        $this->loadJob();
        $componentRouteSegment = $this->getFamilyRouteSegment();        
        $selectEducations = $this->selectEducations();
        if ($componentRouteSegment->hasChildId()) {        
            $companyJob = [
                //route
                'actionSave' => $componentRouteSegment->getSavePath(),
                'actionRemove' => $componentRouteSegment->getRemovePath(),
                // data
                'fields' => [
                    'dataRedApiUri' => "events/v1/data/job/{$this->job->getId()}/jobtotag",  // pro cascade volání vnořeného komponentu JobFamilyTag
                    'published' => $this->job->getPublished(),
                    'pozadovaneVzdelaniStupen' =>  $this->job->getPozadovaneVzdelaniStupen(),
                    'nazev' =>  $this->job->getNazev(),                
                    'mistoVykonu' =>  $this->job->getMistoVykonu(),
                    'popisPozice' =>  $this->job->getPopisPozice(),
                    'pozadujeme' =>  $this->job->getPozadujeme(),
                    'nabizime' =>  $this->job->getNabizime(),                    
                    'selectEducations' =>  $selectEducations,
                    ],                
                ];                
        } elseif ($this->isDataEditable()) {
            $companyJob = [
                // text
                'addHeadline' => 'Přidej pozici', 
                //route
                'actionAdd' => $componentRouteSegment->getAddPath(),
                // data
                'fields' => [
                // cascade
                    'dataRedApiUri' => false,                    
                    'selectEducations' =>  $selectEducations,
                    ],
                ];
        } else {
            $companyJob = [];
        }
                                  
        $this->appendData($companyJob);
        return parent::getIterator();        
    }
    
    
    
    
    
}    

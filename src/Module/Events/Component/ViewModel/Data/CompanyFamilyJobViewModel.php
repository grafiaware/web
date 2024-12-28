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
        } else {
            $cls = JobInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isItemEditable(): bool {
        $this->loadJob();
        return $this->isAdministrator() || $this->isCompanyEditor($this->job->getCompanyId());
    }

    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
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
    
    private function loadJob() {
        if (!isset($this->job)) {
            if ($this->hasItemId()) {
                $this->job = $this->jobRepo->get($this->getItemId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator() {  
        $this->loadJob();
        $componentRouteSegment = $this->getFamilyRouteSegment()->getPath();
        $editableItem = $this->isItemEditable();
        $id = $this->job->getCompanyId();        
        $selectEducations = $this->selectEducations();

        if (isset($id)) {                  
            $companyJob = [
                // conditions
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                'remove'=> $editableItem,   // přidá tlačítko remove do item
                //route
                'componentRouteSegment' => $componentRouteSegment,
                'id' => $this->job->getId(),
                // data
                'fields' => [
                    'editable' => $editableItem,
                    'pozadovaneVzdelaniStupen' =>  $this->job->getPozadovaneVzdelaniStupen(),
                    'nazev' =>  $this->job->getNazev(),                
                    'mistoVykonu' =>  $this->job->getMistoVykonu(),
                    'popisPozice' =>  $this->job->getPopisPozice(),
                    'pozadujeme' =>  $this->job->getPozadujeme(),
                    'nabizime' =>  $this->job->getNabizime(),                    
                    'selectEducations' =>  $selectEducations,
                    ],                
                ];                
        } elseif ($editableItem) {
            $companyJob = [
                // conditions
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                // text
                'addHeadline' => 'Přidej pozici', 
                //route
                'componentRouteSegment' => $componentRouteSegment,
                'id' => $this->job->getId(),
                // data
                'fields' => [
                    'editable' => $editableItem,
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

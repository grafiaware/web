<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelItemAbstract;
use Component\ViewModel\ViewModelItemInterface;

use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\PozadovaneVzdelaniRepoInterface;
use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\JobInterface;
use Access\Enum\RoleEnum;

use Exception;

/**
 * 
 */
class CompanyJobViewModel extends ViewModelItemAbstract implements ViewModelItemInterface {
    
    private $status;
    private $companyRepo;
    private $jobRepo;
    private $pozadovaneVzdelaniRepo;

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

    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyRepresentative($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
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
    
    public function getIterator() {                         
        if ($this->hasItemId()) {
             /** @var JobInterface $job */ 
            $job = $this->jobRepo->get($this->getItemId());  // id jobu
        } else {
            throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
        }
        if (!isset($job)) {
            throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content            
        }
        
        $selectEducations = $this->selectEducations();
        
        $isAdministrator = $this->isAdministrator();
        $editableItem = $isAdministrator || $this->isCompanyRepresentative($job->getCompanyId());
        
        $companyJob = [
            // conditions
            'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
            'remove'=> $isAdministrator,   // přidá tlačítko remove do item
            //route
            'componentRouteSegment' => 'events/v1/job',
            'id' => $job->getId(),
            // data
                'fields' => [
                    'editable' => $editableItem,
//                            'companyId' => $jobEntity->getCompanyId(),                
                    'pozadovaneVzdelaniStupen' =>  $job->getPozadovaneVzdelaniStupen(),
                    'nazev' =>  $job->getNazev(),                
                    'mistoVykonu' =>  $job->getMistoVykonu(),
                    'popisPozice' =>  $job->getPopisPozice(),
                    'pozadujeme' =>  $job->getPozadujeme(),
                    'nabizime' =>  $job->getNabizime(),                    
                    'selectEducations' =>  $selectEducations,
                    ],                
            ];                
                                  
        $this->appendData($companyJob);
        return parent::getIterator();        
    }
    
    
    
    
    
}    

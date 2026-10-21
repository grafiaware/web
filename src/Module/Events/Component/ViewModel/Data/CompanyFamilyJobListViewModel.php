<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyListAbstract;
use Component\ViewModel\ViewModelLimitedListInterface;

use Events\Component\ViewModel\Data\RepresentativeTrait;
use Component\ViewModel\FamilyInterface;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\PozadovaneVzdelaniRepoInterface;
use Events\Model\Repository\CompanyParameterRepoInterface;

use Events\Model\Entity\Job;
use Events\Model\Entity\CompanyParameterInterface;

use Access\Enum\RoleEnum;

use ArrayIterator;
use LogicException;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyFamilyJobListViewModel extends ViewModelFamilyListAbstract implements ViewModelLimitedListInterface {
    
    private $status;
    private $companyRepo;
    private $jobRepo;
    private $pozadovaneVzdelaniRepo;
    private $companyParameterRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            JobRepoInterface $jobRepo,
            PozadovaneVzdelaniRepoInterface $pozadovaneVzdelaniRepo,
            CompanyParameterRepoInterface $companyParameterRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->jobRepo = $jobRepo;
        $this->pozadovaneVzdelaniRepo = $pozadovaneVzdelaniRepo;    
        $this->companyParameterRepo = $companyParameterRepo;
    }    
    
    use RepresentativeTrait;        
    
    public function isListEditable(): bool {
        return $this->isCompanyEditor($this->getFamilyRouteSegment()->getParentId());
    }
        
    public function isItemCountUnderLimit(): bool {
        $companyParameter = $this->companyParameterRepo->get($this->getFamilyRouteSegment()->getParentId());
        if (isset($companyParameter)) {
            $this->loadListEntities();
            $count = count($this->listEntities);            
            $under = $count<($companyParameter->getJobLimit());
        }
        return $under ?? true;   // když není limit (parameter) je vždy true (přidávání povoleno)
    }
    
    protected function newListEntity() {
        return new Job();  // "prázdná" entita pro formulář pro přidání
    }

    protected function cardinality() {
        return FamilyInterface::CARDINALITY_0_N;
    }    

    protected function loadListEntities() {
        if (!$this->listEntities) {
            $company = $this->companyRepo->get($this->getFamilyRouteSegment()->getParentId() );   
            if ($this->isListEditable()) {
                $this->listEntities = $this->jobRepo->find("company_id = :companyId", ['companyId' => $company->getId()]);
            } else {
                $this->listEntities = $this->jobRepo->find("company_id = :companyId AND published=:published", ['companyId' => $company->getId(), 'published'=>1]);                
            }
        }
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
    
    /**
     * Poskytuje data pro šablonu list - pro šablonu, která obaluje pro jednotlivé položky
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator {
        $array = [         
            'listHeadline'=>'Pozice', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();     }
}

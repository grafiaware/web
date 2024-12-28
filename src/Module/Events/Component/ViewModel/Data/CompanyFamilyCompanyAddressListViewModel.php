<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyListAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;
use Component\ViewModel\FamilyInterface;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyAddressRepoInterface;
use Events\Model\Entity\CompanyAddress;

use Access\Enum\RoleEnum;
use ArrayIterator;

/**
 * 
 */
class CompanyFamilyCompanyAddressListViewModel extends ViewModelFamilyListAbstract {

    private $status;

    private $companyRepo;
    private $companyAddressRepo;
    
    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyAddressRepoInterface $companyAddressRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyAddressRepo = $companyAddressRepo;
    }

    use RepresentativeTrait;
    
    public function isListEditable(): bool {
        return $this->isAdministrator() || $this->isCompanyEditor($this->getFamilyRouteSegment()->getParentId());
    }
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }
    
    protected function newListEntity() {
        return new CompanyAddress();  // "prázdná" entita pro formulář pro přidání
    }
    
    protected function cardinality() {
        return FamilyInterface::CARDINALITY_0_1;
    }
    
    protected function loadListEntities() {
        if (!$this->listEntities) {
            $company = $this->companyRepo->get($this->getFamilyRouteSegment()->getParentId() );
//            $this->listEntities[] = $this->companyAddressRepo->get($parentEntity->getId());  // pk = fk     
            $this->listEntities = $this->companyAddressRepo->find( " company_id = :idCompany ",  ['idCompany'=> $company->getId()] );
        }
    }
    
    /**
     * 
     * @return ArrayIterator
     */
    public function getIterator() {
        $array = [         
            'listHeadline'=>'Adresa', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();        
    }
    
    
    
    
    
}    

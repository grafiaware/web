<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyListAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyAddressRepoInterface;
use Events\Model\Entity\CompanyAddress;

use Access\Enum\RoleEnum;

/**
 * 
 */
class CompanyFamilyCompanyAddressListViewModel extends ViewModelFamilyListAbstract {

    private $status;
    private $companyAddressRepo;

    use RepresentativeTrait;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyAddressRepoInterface $companyAddressRepo
            ) {
        $this->status = $status;
        $this->companyAddressRepo = $companyAddressRepo;
    }
    
    public function isListEditable(): bool {
        return $this->isAdministrator();
    }    
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyEditor($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }
    
    public function provideItemEntityCollection(): iterable {
        $entities = $this->companyAddressRepo->findAll();
        if ($this->isAdministrator()) {
            $entities[] = new CompanyAddress();  // pro přidání
        }
        return $entities;
    }
    
    public function getIterator() {
        $array = [         
            'listHeadline'=>'Adresa', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();        
    }
    
    
    
    
    
}    

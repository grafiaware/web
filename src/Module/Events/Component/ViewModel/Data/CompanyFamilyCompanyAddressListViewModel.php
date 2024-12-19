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
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }    
    
    public function provideItemEntityCollection(): iterable {
        // varianta pro vazbu 1:0..1 - kolekce s max. jedním prvkem -> přidat jen pokud není žádný prvek
        
        /** @var CompanyAddressInterface $companyAddress */
        $entities = [];
        $entity = $this->companyAddressRepo->get($this->familyRouteSegment->getParentId());  // pk = fk        
        if ($this->isListEditable() && !$this->count($entities)) {   // přidat jen pokud adresa není
            $entity = new CompanyAddress();  // pro přidání
        }
        if (isset($entity)) {
            $entities[] = $entity;
        } else {
            $entities = [];
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

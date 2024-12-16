<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyListAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyContactRepoInterface;
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\CompanyInterface;

use Access\Enum\RoleEnum;
use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyFamilyCompanyContactListViewModel extends ViewModelFamilyListAbstract {
    
    private $status;
    private $companyRepo;
    private $companyContactRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyContactRepoInterface $companyContactRepo            
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyContactRepo = $companyContactRepo;
    }
    
    public function isListEditable(): bool {
        return $this->isAdministrator();
    }    

    use RepresentativeTrait;
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyEditor($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }
    
    public function provideItemEntityCollection(): iterable {
        $componentRouteSegment = "events/v1/".$this->getFamilyRouteSegment();
        $companyId = $this->getParentId();
        $editableItem = $this->isAdministrator() || $this->isCompanyEditor($companyId);        
        $items = [];
        /** @var CompanyContactInterface $companyContact */
        $companyContacts = $this->companyContactRepo->find( " company_id = :idCompany ",  ['idCompany'=> $companyId ] );

        return $items;
    }
    public function getIterator() {

        $array = [         
            'listHeadline'=>'Kontakty', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();        
    }
}
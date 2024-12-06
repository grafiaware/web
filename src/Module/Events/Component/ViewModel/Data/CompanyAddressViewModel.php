<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelItemAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyAddressRepoInterface;

use Access\Enum\RoleEnum;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyAddressViewModel extends ViewModelItemAbstract {
    
    private $status;
    private $companyRepo;
    private $companyAddressRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyAddressRepoInterface $addressRepo            
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyAddressRepo = $addressRepo;
    }

    use RepresentativeTrait;
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyEditor($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }
    
    public function getIterator() {
        $requestedId = $this->getItemId();
        $componentRouteSegment = "events/v1/companycontact";

        $companyAddress = $this->companyAddressRepo->get($requestedId);
        
        $editableItem = $this->isAdministrator() || $this->isCompanyEditor($companyAddress->getCompanyId());
        $companyContactArray = [
            // conditions
            'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
            'remove'=> $editableItem,   // přidá tlačítko remove do item
            //route
            'componentRouteSegment' => $componentRouteSegment,
            'id' => $companyAddress->getId(),
            // data
            'fields' => [
                'editable' => $editableItem,
                'name'   => $companyAddress->getName(),
                'lokace' => $companyAddress->getLokace(),
                'psc'    => $companyAddress->getPsc(),
                'obec'   => $companyAddress->getObec()
                ],                      
            ];

        $this->appendData($companyContactArray);
        return parent::getIterator();        
    }
}

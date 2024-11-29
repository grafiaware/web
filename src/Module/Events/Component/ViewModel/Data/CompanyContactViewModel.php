<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelItemAbstract;
use Component\ViewModel\ViewModelItemInterface;
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
class CompanyContactViewModel extends ViewModelItemAbstract implements ViewModelItemInterface {
    
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

        $companyContactArray = [];
        $companyContact = $this->companyContactRepo->get($requestedId);
        
        $editableItem = $this->isAdministrator() || $this->isCompanyEditor($companyContact->getCompanyId());
        $companyContactArray = [

            // conditions
            'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
            'remove'=> $editableItem,   // přidá tlačítko remove do item
            // text
            'headline' => 'Kontakt firmy',
            //route
            'componentRouteSegment' => $componentRouteSegment,
            'id' => $companyContact->getId(),
            // data,
            'name' =>  $companyContact->getName(),
            'phones' =>  $companyContact->getPhones(),
            'mobiles' =>  $companyContact->getMobiles(),
            'emails' =>  $companyContact->getEmails(),                       
            ];

        return new ArrayIterator($companyContactArray);
        
    }
}

<?php
namespace Events\Component\ViewModel\Data;

use Events\Model\Entity\RepresentativeInterface;
use Access\Enum\RoleEnum;


use UnexpectedValueException;
/**
 * Description of RepresentativeViewModelAbstract
 *
 * @author pes2704
 */
trait RepresentativeTrait {

    protected function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }
    
    protected function getStatusRepresentative(): ?RepresentativeInterface {
        $representativeActions = $this->status->getRepresentativeActions();
        return isset($representativeActions) ? $representativeActions->getRepresentative() : null;    
    }
    
    protected function getStatusRepresentativeCompanyId(): ?string {
        $representative = $this->getStatusRepresentative();
        return isset($representative) ? $representative->getCompanyId() : null;
    }
    
    protected function getStatusRepresentativeDataEditable(): bool {
        $actions = $this->status->getRepresentativeActions();
        return isset($actions) ? $actions->getDataEditable() : false;
    }
    
    protected function isCompanyEditor($companyId): bool {
        if(!isset($companyId)) {
            throw new UnexpectedValueException("Hodnota id company nesmí být null.");
        }
        $editData = $this->getStatusRepresentativeDataEditable();
        $isCompanyRepresentative = ($this->getStatusRepresentativeCompanyId()==$companyId);
        $isAdministrator = $this->isAdministrator();
        return ($editData AND ($isCompanyRepresentative OR $isAdministrator));
    }
    
    protected function isAdministratorEditor() {
        return ($this->getStatusRepresentativeDataEditable() AND $this->isAdministrator());        
    }
}

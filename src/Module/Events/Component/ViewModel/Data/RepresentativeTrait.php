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

    /**
     * Přihlášený uživatel má roli events administrator
     * @return bool
     */
    protected function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }
    
    /**
     * Vrací status entitu RepresentativeInterface pokud exestují representative actions.
     * @return RepresentativeInterface|null
     */
    protected function getStatusRepresentative(): ?RepresentativeInterface {
        $representativeActions = $this->status->getRepresentativeActions();
        return isset($representativeActions) ? $representativeActions->getRepresentative() : null;    
    }
    
    /**
     * Vrací id company přihlášeného reprezentanta. Pokud není přihlášen reprezentant vrací null.
     * @return string|null
     */
    protected function getStatusRepresentativeCompanyId(): ?string {
        $representative = $this->getStatusRepresentative();
        return isset($representative) ? $representative->getCompanyId() : null;
    }
    
    /**
     * Informuje zda přihlášený reprezentant má zapnutou edditaci
     * @return bool
     */
    protected function getStatusRepresentativeDataEditable(): bool {
        $actions = $this->status->getRepresentativeActions();
        return isset($actions) ? $actions->getDataEditable() : false;
    }

    /**
     * Informuje zda přihlášený uživatel reprezentuje company.
     * 
     * @param type $companyId
     * @return bool
     * @throws UnexpectedValueException
     */
    protected function isCompanyRepresentative($companyId): bool {
        if(!isset($companyId)) {
            throw new UnexpectedValueException("Hodnota id company nesmí být null.");
        }
        return ($this->getStatusRepresentativeCompanyId()==$companyId);
    }
    
    /**
     * Informuje zda přihlášený uživatel reprezentuje company a právě edituje data.
     * 
     * @param type $companyId
     * @return bool
     * @throws UnexpectedValueException
     */
    protected function isCompanyEditor($companyId): bool {
        if(!isset($companyId)) {
            throw new UnexpectedValueException("Hodnota id company nesmí být null.");
        }
        $editData = $this->getStatusRepresentativeDataEditable();
        $isCompanyRepresentative = $this->isCompanyRepresentative($companyId);
        $isAdministrator = $this->isAdministrator();
        return ($editData AND ($isCompanyRepresentative OR $isAdministrator));
    }
    
    /**
     * Informuje zda přihlášený uživatel je event administrator a má zapnutou editaci.
     * 
     * @return bool
     */
    protected function isAdministratorEditor(): bool {
        return ($this->getStatusRepresentativeDataEditable() OR $this->isAdministrator());        
    }
}

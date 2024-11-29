<?php
namespace Events\Component\ViewModel\Data;

use Events\Model\Entity\RepresentativeInterface;

/**
 * Description of RepresentativeViewModelAbstract
 *
 * @author pes2704
 */
trait RepresentativeTrait {
        
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
    
}

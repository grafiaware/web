<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\RepresentativeInterface;

/**
 * Description of PresenterActions
 *
 * @author pes2704
 */
class RepresentativeActions extends PersistableEntityAbstract implements RepresentativeActionsInterface {
    
    /**
     * 
     * @var RepresentativeInterface
     */
    private $representative;
    
    public function processActionsForLossOfSecurityContext($loggedOffUserName) {
//        if($loggedOffUserName == $this->representative->getLoginLoginName()) {
//            // nedělám nic, je to tu jen jako poznámka
//        }
        unset($this->representative);
    }
    
    public function setRepresentative(RepresentativeInterface $representative) {
        $this->representative = $representative;
    }
    
    public function getRepresentative(): RepresentativeInterface {
        return $this->representative;
    }
}

<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\RepresentativeInterface;

/**
 * Description of PresenterActions
 *
 * @author pes2704
 */
class RepresentationActions extends PersistableEntityAbstract implements RepresentationActionsInterface {
    
    /**
     * 
     * @var RepresentativeInterface
     */
    private $representative;
    
    private $edit;


    public function processActionsForLossOfSecurityContext($loggedOffUserName) {
//        if($loggedOffUserName == $this->representative->getLoginLoginName()) {
//            // nedělám nic, je to tu jen jako poznámka
//        }
        unset($this->representative);
    }
    
    public function setRepresentative(RepresentativeInterface $representative) {
        $this->representative = $representative;
    }
    
    public function getRepresentative(): ?RepresentativeInterface {
        return $this->representative;
    }
    /**
     * Nastaví informaci, že reprezentant přepnul do modu editace dat
     *
     * @param mixed $edit Metoda převede zadanou hodnotu na boolean hodnotu.
     * @return void
     */
    public function setDataEditable($edit): void {
        $this->edit = boolval($edit);
    }    
    
    /**
     * 
     * @return bool
     */
    public function getDataEditable(): bool {
        return (bool) $this->edit ?? false;
    }
}

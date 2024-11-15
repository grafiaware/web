<?php
namespace Events\Model\Entity;
use Model\Entity\SecurityPersistableEntityInterface;
use Events\Model\Entity\RepresentativeInterface;

/**
 *
 * @author pes2704
 */
interface RepresentationActionsInterface extends SecurityPersistableEntityInterface {
    
    const STATUS_INFO_KEY = "representative_actions";    
    
    public function setRepresentative(RepresentativeInterface $representative);
    public function getRepresentative(): ?RepresentativeInterface;
    /**
     * Nastaví informaci, že prezentace je přepnuta do modu editace článku
     *
     * @return void
     */
    public function setDataEditable($edit): void;    
    public function getDataEditable(): bool;    
}

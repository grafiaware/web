<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Model\Entity\StatusSecurity;
use Model\Entity\StatusSecurityInterface;

/**
 * Description of SecurityStatusManager
 *
 * @author pes2704
 */
class StatusSecurityManager implements StatusSecurityManagerInterface {

    /**
     * @var StatusSecurityInterface
     */
    protected $securityStatus;

    private $securityContextObservers;

    public function __construct() {
        $this->securityContextObservers = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     *
     * Metoda smaže objekty UserInterface, AccountInterface a Handler předané jako instanční proměnné při volání konstruktoru.
     * Předpokládá, že tyto proměnné byly do konstruktoru injektovány v kontejneru, pak tím dojde i kesmazání objektů v instancích kontejneru
     * a kontejner při dalším volání služeb automatocky vytvoří tyto objekty nové.
     *
     * @return void
     */
    public function newSecurityStatus(): StatusSecurityInterface {
        $this->notify();
        $this->securityStatus = new StatusSecurity();
        return $this->securityStatus;
    }

    public function attach(\SplObserver $observer): void {
        $this->securityContextObservers->attach($observer);
    }

    public function detach(\SplObserver $observer): void {
        $this->securityContextObservers->detach($observer);
    }

    public function notify(): void {
        foreach ($this->securityContextObservers as $observer) {
            /** @var \SplObserver $observer */
            $observer->update($this);
        }
    }
}

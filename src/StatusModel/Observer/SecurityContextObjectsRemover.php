<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager\Observer;

/**
 * Description of SecurityContextObjectsRemover
 *
 * @author pes2704
 */
class SecurityContextObjectsRemover implements SucurityContextObjectsRemoverInterface {

    private $contextObjects;

    public function __construct() {
        $this->contextObjects = new \SplObjectStorage();
    }

    public function attachContextObject($object) {
        $this->contextObjects->attach($object);
        return $this;
    }

    public function detachContextObject($object) {
        $this->contextObjects->detach($object);
        return $this;
    }

    /**
     * Vždy odstraní (unset) všechny připojené objekty bezpečnostního kontextu.
     * @param \SplSubject $subject
     * @return void
     */
    public function update(\SplSubject $subject): void {
        foreach ($this->contextObjects as $object) {
            unset($object);
        };
    }
}

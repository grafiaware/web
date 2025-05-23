<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Repository;

use Model\Repository\StatusRepositoryAbstract;

use Status\Model\Entity\SecurityInterface;

/**
 * Description of StatusSecurityRepo
 * Repository obsahuje vždy jen jednu entitu StatusSecurity.
 *
 * @author pes2704
 */
class StatusSecurityRepo extends StatusRepositoryAbstract {

    const FRAGMENT_NAME = 'security';

    /**
     * Repository obsahuje vždy jen jednu entitu StatusSecurityInterface. Pokud entita ješte nebyla načtena z úložiště,
     * načte ji (jen jednou) a vrací.
     *
     * @return SecurityInterface|null
     */
    public function get(): ?SecurityInterface {
        if (! isset($this->entity)) {
            $this->load();
        }
        return $this->entity;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu StatusSecurityInterface. Metoda add přidá entitu do prázdného repository, pokud v repository již entita je, přepíše ji.
     * @param SecurityInterface $securityStatus
     */
    public function add(SecurityInterface $securityStatus) {
        $this->entity = $securityStatus;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu StatusSecurityInterface a ta je smazána.
     */
    public function remove() {
        $this->entity = NULL;
    }
}

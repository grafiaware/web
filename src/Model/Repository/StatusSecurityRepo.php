<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\StatusSecurityInterface;

use Model\Dao\StatusDao;
use Model\Repository\UserRepo;

/**
 * Description of StausLoginRepo
 * Repository obsahuje vždy jen jednu entitu StatusLogin.
 *
 * @author pes2704
 */
class StatusSecurityRepo extends StatusRepositoryAbstract {

    const FRAGMENT_NAME = 'security';

    /**
     * Repository obsahuje vždy jen jednu entitu StatusSecurityInterface. Pokud entita ješte nebyla načtena z úložiště,
     * načte ji (jen jednou) a vrací.
     *
     * @return StatusSecurityInterface|null
     */
    public function get(): ?StatusSecurityInterface {
        if (! isset($this->entity)) {
            $this->load();
        }
        return $this->entity;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu StatusSecurityInterface. Metoda add přidá entitu do prázdného repository, pokud v repository již entita je, přepíše ji.
     * @param StatusSecurityInterface $securityStatus
     */
    public function add(StatusSecurityInterface $securityStatus) {
        $this->entity = $securityStatus;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu StatusSecurityInterface a ta je smazána.
     */
    public function remove() {
        $this->entity = NULL;
    }
}

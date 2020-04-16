<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\StatusFlashInterface;

/**
 * Description of StausLoginRepo
 * Repository obsahuje vždy jen jednu entitu StatusLogin.
 *
 * @author pes2704
 */
class StatusFlashRepo extends StatusRepositoryAbstract {

    const FRAGMENT_NAME = 'flash';

    /**
     * Repository obsahuje vždy jen jednu entitu. Pokud entita ješte nebyla načtena z úložiště,
     * načte ji (jen jednou) a vrací.
     *
     * @return StatusFlashInterface|null
     */
    public function get(): ?StatusFlashInterface {
        if (! isset($this->entity)) {
            $this->load();
        }
        return $this->entity;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu. Metoda add přidá entitu do prázdného repository, pokud v repository již entita je, přepíše ji.
     * @param StatusFlashInterface $flashStatus
     */
    public function add(StatusFlashInterface $flashStatus) {
        $this->entity = $flashStatus;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu a ta je smazána.
     */
    public function remove() {
        $this->entity = NULL;
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Repository;

use Model\Repository\StatusRepositoryAbstract;

use Status\Model\Entity\FlashInterface;

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
     * @return FlashInterface|null
     */
    public function get(): ?FlashInterface {
        if (! isset($this->entity)) {
            $this->load();
        }
        return $this->entity;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu. Metoda add přidá entitu do prázdného repository, pokud v repository již entita je, přepíše ji.
     * @param FlashInterface $flashStatus
     */
    public function add(FlashInterface $flashStatus) {
        $this->entity = $flashStatus;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu a ta je smazána.
     */
    public function remove() {
        $this->entity = NULL;
    }
}

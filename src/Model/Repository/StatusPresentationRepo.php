<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\StatusPresentationInterface;

/**
 * Description of StausPresentationRepo
 * Repository obsahuje vždy jen jednu entitu StatusPresentation.
 *
 * @author pes2704
 */
class StatusPresentationRepo extends StatusRepositoryAbstract {

    const FRAGMENT_NAME = 'presentation';

    /**
     * Repository obsahuje vždy jen jednu entitu StatusPresentation. Pokud entita ješte nebyla načtena z úložiště,
     * načte ji (jen jednou) a vrací.
     *
     * @return StatusPresentationInterface|null
     */
    public function get(): ?StatusPresentationInterface {
        if (! isset($this->entity)) {
            $this->load();
        }
        return $this->entity;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu StatusLogin. Metoda add přidá entitu do prázdného repository, pokud v repository již entita je, přepíše ji.
     *
     * @param StatusPresentationInterface $statusPresentation
     */
    public function add(StatusPresentationInterface $statusPresentation) {
        $this->entity = $statusPresentation;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu StatusPresentationInterface a ta je smazána.
     */
    public function remove() {
        $this->entity = NULL;
    }
}

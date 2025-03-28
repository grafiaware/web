<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Repository;

use Model\Repository\StatusRepositoryAbstract;

use Status\Model\Entity\PresentationInterface;

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
     * @return PresentationInterface|null
     */
    public function get(): ?PresentationInterface {
        if (! isset($this->entity)) {
            $this->load();
        }
        return $this->entity;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu StatusPresentation. Metoda add přidá entitu do prázdného repository, pokud v repository již entita je, přepíše ji.
     *
     * @param PresentationInterface $statusPresentation
     */
    public function add(PresentationInterface $statusPresentation) {
        $this->entity = $statusPresentation;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu StatusPresentationInterface a ta je smazána.
     */
    public function remove() {
        $this->entity = NULL;
    }
}

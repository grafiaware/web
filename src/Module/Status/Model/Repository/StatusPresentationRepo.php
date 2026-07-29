<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Repository;

use Pes\Model\Repository\StatusRepositoryAbstract;

use Status\Model\Entity\PresentationInterface;

/**
 * Description of StausPresentationRepo
 * Repository obsahuje vždy jen jednu entitu StatusPresentation.
 *
 * Po StatusDao::finish() get/add/remove vyhodí SessionFinishedException;
 * pro read-only snapshot použijte isFinished() + getClone().
 *
 * @author pes2704
 */
class StatusPresentationRepo extends StatusRepositoryAbstract {

    const FRAGMENT_NAME = 'presentation';

    /**
     * Repository obsahuje vždy jen jednu entitu StatusPresentation. Pokud entita ješte nebyla načtena z úložiště,
     * načte ji (jen jednou) a vrací.
     *
     * @return PresentationInterface
     */
    public function get(): ?PresentationInterface {
        $this->assertSessionWritableForGet();
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
        $this->assertSessionWritable('add');
        $this->entity = $statusPresentation;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu StatusPresentationInterface a ta je smazána.
     */
    public function remove() {
        $this->assertSessionWritable('remove');
        $this->entity = NULL;
    }
}

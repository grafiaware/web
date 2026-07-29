<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Repository;

use Pes\Model\Repository\StatusRepositoryAbstract;

use Status\Model\Entity\FlashInterface;

/**
 * Description of StausLoginRepo
 * Repository obsahuje vždy jen jednu entitu StatusLogin.
 *
 * Po StatusDao::finish() get/add/remove vyhodí SessionFinishedException;
 * pro read-only snapshot použijte isFinished() + getClone().
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
        $this->assertSessionWritableForGet();
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
        $this->assertSessionWritable('add');
        $this->entity = $flashStatus;
    }

    /**
     * Repository obsahuje vždy jen jednu entitu a ta je smazána.
     */
    public function remove() {
        $this->assertSessionWritable('remove');
        $this->entity = NULL;
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Model\Entity\StatusPresentationInterface;

/**
 *
 * @author pes2704
 */
interface StatusPresentationManagerInterface {

    /**
     *
     * @param StatusPresentationInterface $statusPresentation
     * @return StatusPresentationInterface
     */
    public function regenerateStatusPresentation(StatusPresentationInterface $statusPresentation): void;

}

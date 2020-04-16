<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\EntityFactory;

use Model\Entity\StatusPresentationInterface;
use Model\Entity\StatusPresentation;

/**
 * Description of StatusPresentationFactory
 *
 * @author pes2704
 */
class StatusPresentationFactory implements EntityFactoryInterface {

    /**
     * @return StatusPresentationInterface
     */
    public function create() {
        return new StatusPresentation();
    }
}

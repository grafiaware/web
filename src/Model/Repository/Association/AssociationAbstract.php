<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Hydrator\HydratorInterface;


/**
 * Description of AssotiationAbstract
 *
 * @author pes2704
 */
class AssociationAbstract implements AssociationInterface {

    public function flushChildRepo(): void {
        $this->childRepo->flush();
    }
}

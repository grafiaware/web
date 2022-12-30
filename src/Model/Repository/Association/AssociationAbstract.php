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

    protected $referenceName;

    /**
     *
     * @param array $childKeyAttribute Atribut cizího klíče, klíče který je referencí na data rodiče v úložišti dat . V databázi jde o cizí klíč v potomkovské tabulce.
     */
    protected function __construct(string $referenceName) {
        $this->referenceName = $referenceName;
    }

    public function flushChildRepo(): void {
        $this->childRepo->flush();
    }
}

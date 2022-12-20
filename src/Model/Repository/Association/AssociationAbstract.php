<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;
use Model\Hydrator\RowHydratorInterface;
use Model\Hydrator\AssotiationHydratorInterface;


/**
 * Description of AssotiationAbstract
 *
 * @author pes2704
 */
class AssociationAbstract implements AssociationInterface {

    protected $parentTable;

    protected $childHydrator;

    /**
     *
     * @param array $childKeyAttribute Atribut cizího klíče, klíče který je referencí na data rodiče v úložišti dat . V databázi jde o cizí klíč v potomkovské tabulce.
     */
    protected function __construct(string $parentTable, AssotiationHydratorInterface $childHydrator) {
        $this->parentTable = $parentTable;
        $this->childHydrator = $childHydrator;
    }

    public function addAssociatedEntity(EntityInterface $entity = null): void {
        $this->childRepo->add($entity);
    }

    public function removeAssociatedEntity(EntityInterface $entity = null): void {
        $this->childRepo->remove($entity);
    }

    public function flushChildRepo(): void {
        $this->childRepo->flush();
    }
}

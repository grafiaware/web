<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\ComponentInterface;
use Pes\Type\Date;

/**
 * Description of MenuItemHydrator
 *
 * @author pes2704
 */
class ComponentHydrator implements HydratorInterface {

    /**
     *
     * @param MenuItemInterface $component
     * @param type $row
     */
    public function hydrate(EntityInterface $component, &$row) {
        /** @var ComponentInterface $component */
        $component
                ->setName($row['name'])
                ->setUidFk($row['uid_fk'])
            ;
    }

    /**
     *
     * @param MenuItemInterface $component
     * @param type $row
     */
    public function extract(EntityInterface $component, &$row=[]) {
        /** @var ComponentInterface $component */
        $row['name'] = $component->getName();
        $row['uid_fk'] = $component->getUidFk();
    }
}

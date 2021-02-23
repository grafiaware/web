<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\LoginAggregateInterface;

/**
 * Description of CredentialsChildHydrator
 *
 * @author pes2704
 */
class LoginChildHydrator implements HydratorInterface {

    /**
     *
     * @param LoginAggregateInterface $loginAggregate
     * @param type $row
     */
    public function hydrate(EntityInterface $loginAggregate, &$row) {
        /** @var LoginAggregateInterface $loginAggregate */
        $loginAggregate
            ->setCredentials($row['credentials']);
    }

    /**
     *
     * @param LoginAggregateInterface $loginAgregate
     * @param type $row
     */
    public function extract(EntityInterface $loginAgregate, &$row) {
        /** @var LoginAggregateInterface $loginAgregate */
        $row['credentials'] = $loginAgregate->getCredentials();
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\RowHydratorInterface;
use Model\RowData\RowDataInterface;

use Auth\Model\Entity\LoginAggregateCredentialsInterface;
use Auth\Model\Entity\CredentialsInterface;

/**
 * Description of CredentialsChildHydrator
 *
 * @author pes2704
 */
class LoginChildCredentialsHydrator implements RowHydratorInterface {

    /**
     *
     * @param LoginAggregateCredentialsInterface $loginAggregate
     * @param type $rowData
     */
    public function hydrate(EntityInterface $loginAggregate, RowDataInterface $rowData) {
        /** @var LoginAggregateCredentialsInterface $loginAggregate */
        $loginAggregate
            ->setCredentials($rowData->offsetGet(CredentialsInterface::class));
    }

    /**
     *
     * @param LoginAggregateCredentialsInterface $loginAgregate
     * @param type $rowData
     */
    public function extract(EntityInterface $loginAgregate, RowDataInterface $rowData) {
        /** @var LoginAggregateCredentialsInterface $loginAgregate */
        $rowData->offsetSet(CredentialsInterface::class, $loginAgregate->getCredentials());
    }

}
